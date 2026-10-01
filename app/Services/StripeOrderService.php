<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Verify a Stripe PaymentIntent and create the order from the trusted
 * PendingCheckout snapshot — the Stripe counterpart of PaystackOrderService.
 *
 * Nothing browser-supplied is trusted for pricing: saveCheckout() computes the
 * NGN total from the database before the card form is shown, and the USD charge
 * amount is derived server-side from that total using the configured rate. This
 * service re-checks the PaymentIntent against Stripe (status, currency, amount,
 * and the reference stamped into its metadata) before a single order row is written.
 */
class StripeOrderService
{
    /**
     * @return array{success:bool, order_id:?int, order_number:?string, duplicate:bool, error_type:?string, message:string}
     */
    public function fulfil(string $reference, string $paymentIntentId, ?int $userId): array
    {
        // 1. The trusted snapshot must exist and be unfulfilled.
        $pending = PendingCheckout::where('reference', $reference)
            ->whereNull('fulfilled_at')
            ->first();

        // Idempotency: if the order already exists (double submit / retry), return it.
        $existing = Order::where('reference', $reference)
            ->orWhere('payment_id', $paymentIntentId)
            ->first();
        if ($existing) {
            return $this->result(true, $existing->id, $existing->order_number, true, null, 'Order already exists.');
        }

        if ($pending === null) {
            Log::alert(
                "Stripe: payment intent {$paymentIntentId} confirmed but no PendingCheckout found for ref={$reference}. " .
                "Customer may have paid and received nothing. Manual review required."
            );
            return $this->result(false, null, null, false, null, 'No checkout session found. Contact support with reference: ' . $reference);
        }

        if (empty($pending->items)) {
            Log::alert("Stripe: PendingCheckout for ref={$reference} has no items. Manual review required.");
            return $this->result(false, null, null, false, null, 'Cart snapshot is empty for this reference.');
        }

        // 2. Retrieve and verify the PaymentIntent with Stripe.
        try {
            $intent = $this->retrievePaymentIntent($paymentIntentId);
        } catch (\Throwable $e) {
            // Network / API problem — transient, the browser (or a retry) can try again.
            Log::error("Stripe verify exception for {$paymentIntentId}: " . $e->getMessage());
            return $this->result(false, null, null, false, 'transient', 'Could not reach Stripe to verify the payment.');
        }

        if ($intent === null) {
            return $this->result(false, null, null, false, null, 'Payment could not be verified.');
        }

        $status   = $intent['status']   ?? '';
        $currency = strtolower($intent['currency'] ?? '');
        $amount   = (int) ($intent['amount_received'] ?? $intent['amount'] ?? 0);
        $metaRef  = $intent['metadata']['reference'] ?? null;

        if ($status !== 'succeeded') {
            Log::warning("Stripe payment not succeeded ref={$reference} pi={$paymentIntentId} status={$status}");
            return $this->result(false, null, null, false, null, 'Payment has not completed.');
        }

        if ($currency !== 'usd') {
            Log::warning("Stripe currency mismatch ref={$reference}: received={$currency}");
            return $this->result(false, null, null, false, 'fraud', 'Currency mismatch.');
        }

        // The PaymentIntent must belong to THIS checkout — prevents replaying a
        // valid intent from a different (cheaper) session against this reference.
        if ($metaRef !== null && $metaRef !== $reference) {
            Log::warning("Stripe metadata reference mismatch: intent ref={$metaRef}, expected={$reference}");
            return $this->result(false, null, null, false, 'fraud', 'Payment does not match this order.');
        }

        // 3. Amount check — recompute the expected USD cents from the NGN snapshot
        //    using the same server-side rate the intent was created with.
        $expectedCents = $this->expectedUsdCents((float) $pending->total_ngn);
        if ($amount < $expectedCents - 1) { // 1-cent tolerance for rounding
            Log::warning("Stripe underpayment ref={$reference}: expected>={$expectedCents} cents, got={$amount} cents");
            return $this->result(false, null, null, false, 'fraud', 'Amount underpaid.');
        }

        // 4. Create the order from the snapshot in a single transaction.
        $items          = $pending->items;
        $fulfillment    = $pending->fulfillment    ?? [];
        $couponData     = $pending->coupon         ?? null;
        $custEmail      = $pending->customer_email ?? null;
        $resolvedUserId = $pending->user_id        ?? $userId;
        $totalUsd       = $amount / 100;

        try {
            $order = DB::transaction(function () use (
                $reference, $paymentIntentId, $resolvedUserId, $pending, $fulfillment,
                $items, $couponData, $custEmail, $totalUsd
            ) {
                $order = Order::create([
                    'user_id'                => $resolvedUserId,
                    'status'                 => 'paid',
                    'total'                  => $pending->total_ngn,
                    'total_usd'              => $totalUsd,
                    'payment_method'         => 'stripe',
                    'payment_id'             => $paymentIntentId,
                    'reference'              => $reference,
                    'fulfillment_method'     => $fulfillment['method']                ?? 'pickup',
                    'pickup_location'        => $fulfillment['pickup_location']        ?? null,
                    'pickup_location_id'     => $fulfillment['pickup_location_id']     ?? null,
                    'pickup_point_id'        => $fulfillment['pickup_point_id']        ?? null,
                    'pickup_point_name'      => $fulfillment['pickup_point_name']      ?? null,
                    'pickup_point_address'   => $fulfillment['pickup_point_address']   ?? null,
                    'delivery_state_id'      => $fulfillment['delivery_state_id']      ?? null,
                    'delivery_state_name'    => $fulfillment['delivery_state_name']    ?? null,
                    'delivery_location_id'   => $fulfillment['delivery_location_id']   ?? null,
                    'delivery_location_name' => $fulfillment['delivery_location_name'] ?? null,
                    'shipping_address'       => $fulfillment['shipping_address']       ?? null,
                    'shipping_cost'          => $fulfillment['delivery_fee_ngn']       ?? 0,
                    'customer_email'         => $custEmail,
                    'coupon_id'              => $couponData['id']           ?? null,
                    'coupon_code_used'       => $couponData['code']         ?? null,
                    'coupon_discount_ngn'    => $couponData['discount_ngn'] ?? 0,
                ]);

                foreach ($items as $item) {
                    $order->items()->create([
                        'product_id'             => $item['id'],
                        'name'                   => $item['name'],
                        'price'                  => $item['effective_price_ngn'],
                        'quantity'               => $item['quantity'],
                        'image'                  => $item['image']                  ?? null,
                        'sku'                    => $item['sku']                    ?? null,
                        // Carry installation options from the trusted snapshot — see
                        // the guard comments in OrderController::saveCheckout.
                        'installation_option'    => $item['installation_option']    ?? null,
                        'installation_extra_ngn' => $item['installation_extra_ngn'] ?? 0,
                    ]);
                }

                if (!empty($couponData['id'])) {
                    $coupon = Coupon::lockForUpdate()->find($couponData['id']);
                    if ($coupon) {
                        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
                            Log::warning("Coupon {$coupon->code} limit exhausted at Stripe fulfilment (ref={$reference}); skipping increment.");
                        } else {
                            $coupon->increment('used_count');
                            CouponUsage::create([
                                'coupon_id' => $coupon->id,
                                'user_id'   => $resolvedUserId,
                                'order_id'  => $order->id,
                            ]);
                        }
                    }
                }

                $pending->update(['fulfilled_at' => now()]);

                return $order;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $mysqlDuplicate    = ($e->errorInfo[1] ?? null) === 1062;
            $postgresDuplicate = ($e->errorInfo[0] ?? '') === '23505';

            if (!$mysqlDuplicate && !$postgresDuplicate) {
                Log::error("Stripe order creation DB error for ref={$reference}: " . $e->getMessage());
                return $this->result(false, null, null, false, 'transient', $e->getMessage());
            }

            $existing = Order::where('reference', $reference)->first();
            if ($existing) {
                Log::info("Duplicate Stripe order caught via DB constraint for ref={$reference}");
                return $this->result(true, $existing->id, $existing->order_number, true, null, 'Order already exists.');
            }

            Log::error("Stripe unique violation but order not found for ref={$reference}");
            return $this->result(false, null, null, false, 'transient', 'Unique constraint hit but order not found.');
        } catch (\Exception $e) {
            Log::error("Stripe order creation failed for ref={$reference}: " . $e->getMessage());
            return $this->result(false, null, null, false, 'transient', $e->getMessage());
        }

        $recipient = $order->user?->email ?? $custEmail;
        if ($recipient) {
            try {
                Mail::to($recipient)->send(new OrderConfirmation($order));
            } catch (\Exception $e) {
                Log::error('Stripe order confirmation email failed for order #' . $order->order_number . ': ' . $e->getMessage());
            }
        }

        return $this->result(true, $order->id, $order->order_number, false, null, 'Order created.');
    }

    /**
     * Expected USD charge in cents for an NGN total. Mirrors the browser's
     * display maths (config rate, floor of 50 cents) so both agree exactly.
     */
    public function expectedUsdCents(float $totalNgn): int
    {
        $rate = (float) config('services.stripe.usd_rate', 0.00067);
        return max((int) round($totalNgn * $rate * 100), 50);
    }

    /**
     * Fetch the PaymentIntent from Stripe as an array. Isolated so tests can
     * stub the network call without hitting Stripe.
     *
     * @return array<string,mixed>|null
     */
    protected function retrievePaymentIntent(string $paymentIntentId): ?array
    {
        $secret = config('services.stripe.secret');
        if (!self::keyConfigured($secret)) {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        \Stripe\Stripe::setApiKey($secret);
        $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

        return $intent ? $intent->toArray() : null;
    }

    /** True when a Stripe key is present and not the shipped placeholder. */
    public static function keyConfigured(?string $key): bool
    {
        return !empty($key) && !str_contains($key, 'REPLACE');
    }

    private function result(bool $success, ?int $orderId, ?string $orderNumber, bool $duplicate, ?string $errorType, string $message): array
    {
        return [
            'success'      => $success,
            'order_id'     => $orderId,
            'order_number' => $orderNumber,
            'duplicate'    => $duplicate,
            'error_type'   => $errorType,
            'message'      => $message,
        ];
    }
}
