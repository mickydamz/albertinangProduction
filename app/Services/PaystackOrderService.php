<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\PaystackTransaction;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaystackOrderService
{
    /**
     * Verify a Paystack reference, log the transaction, and create an order.
     * Safe to call from both the browser confirm path and the server webhook —
     * whichever fires first creates the order; the second call is a no-op.
     *
     * All cart data comes from PendingCheckout — nothing browser-supplied is
     * trusted for pricing. saveCheckout() computes prices from the DB before
     * the popup opens, so PendingCheckout is always the authoritative snapshot.
     *
     * Return shape:
     *   success      bool
     *   order_id     int|null
     *   order_number string|null
     *   duplicate    bool
     *   error_type   'transient'|'fraud'|null   — 'transient' means retry may succeed
     *   message      string
     */
    public function fulfil(string $reference, ?int $userId): array
    {
        // Resolve PendingCheckout early — needed for amount verification and as the
        // sole trusted source of items and totals.
        $pending = PendingCheckout::where('reference', $reference)
            ->whereNull('fulfilled_at')
            ->first();

        if ($userId !== null) {
            $owner = PendingCheckout::where('reference', $reference)->value('user_id');
            $existingOwner = Order::where('reference', $reference)->value('user_id');
            if (($owner !== null && (int) $owner !== $userId) || ($existingOwner !== null && (int) $existingOwner !== $userId)) {
                return $this->result(false, null, null, false, 'fraud', 'Checkout session not found.');
            }
        }

        // 1. Verify with Paystack
        try {
            $response = Http::withToken(config('services.paystack.secret'))
                ->timeout(20)
                ->retry(2, 800, throw: false)
                ->get('https://api.paystack.co/transaction/verify/' . $reference);
            $data = $response->json();
        } catch (\Exception $e) {
            Log::error("Paystack verify exception for {$reference}: " . $e->getMessage());
            return $this->result(false, null, null, false, 'transient', 'Payment confirmation is delayed. Keep your reference and try recovery again.');
        }

        // 5xx from Paystack = their API is down, not a failed payment — treat as transient
        // so the webhook returns 503 and Paystack retries. 4xx = permanent failure.
        if ($response->serverError()) {
            Log::warning("Paystack API returned {$response->status()} for ref={$reference}");
            return $this->result(false, null, null, false, 'transient', 'Paystack API unavailable (HTTP ' . $response->status() . ')');
        }

        if (!$response->successful() || ($data['data']['status'] ?? '') !== 'success') {
            $existing = Order::where('reference', $reference)->first();
            if ($existing) {
                return $this->result(true, $existing->id, $existing->order_number, true, null, 'Order already exists.');
            }
            return $this->result(false, null, null, false, null, $data['message'] ?? 'Payment not verified.');
        }

        $paystackData = $data['data'];

        // 2. Verify currency and amount against the PendingCheckout snapshot.
        //    Prevents an attacker from reusing a reference for a cheaper payment.
        $currency = $paystackData['currency'] ?? '';
        if ($currency !== 'NGN') {
            Log::warning("Paystack currency mismatch ref={$reference}: received={$currency}");
            return $this->result(false, null, null, false, 'fraud', 'Currency mismatch.');
        }

        if ($pending !== null) {
            $expectedKobo = (int) round((float) $pending->total_ngn * 100);
            $receivedKobo = (int) ($paystackData['amount'] ?? 0);

            if ($receivedKobo < $expectedKobo) {
                Log::warning("Paystack underpayment ref={$reference}: expected={$expectedKobo} kobo, got={$receivedKobo} kobo");
                return $this->result(false, null, null, false, 'fraud', 'Amount underpaid.');
            }
        }

        // 3. Durable transaction log (idempotent — unique constraint on reference)
        try {
            PaystackTransaction::firstOrCreate(
                ['reference' => $reference],
                [
                    'paystack_id' => $paystackData['id']     ?? null,
                    'amount_kobo' => $paystackData['amount'] ?? 0,
                    'status'      => $paystackData['status'],
                    'payload'     => $paystackData,
                ]
            );
        } catch (\Exception $e) {
            Log::warning("Could not write paystack_transactions for {$reference}: " . $e->getMessage());
        }

        // 4. Idempotency check — order already created (webhook fired twice, or both paths hit)
        $existing = Order::where('reference', $reference)->first();
        if ($existing) {
            return $this->result(true, $existing->id, $existing->order_number, true, null, 'Order already exists.');
        }

        // 5. Require PendingCheckout — the only trusted source of items and totals.
        //    saveCheckout() computes all prices server-side, so this snapshot cannot
        //    be manipulated by the browser. If it is missing, the customer has paid
        //    but will receive no order — this needs immediate admin attention.
        if ($pending === null) {
            Log::alert(
                "Paystack: payment received but no PendingCheckout found for ref={$reference}. " .
                "Customer has paid and received nothing. Manual review required."
            );
            $this->notifyAdminOfMissingCheckout($reference, $paystackData);
            return $this->result(false, null, null, false, null, 'No checkout session found. Contact support with reference: ' . $reference);
        }

        if (empty($pending->items)) {
            Log::alert("Paystack: PendingCheckout for ref={$reference} has no items. Manual review required.");
            return $this->result(false, null, null, false, null, 'Cart snapshot is empty for this reference.');
        }

        $items          = $pending->items;
        $fulfillment    = $pending->fulfillment    ?? [];
        $couponData     = $pending->coupon         ?? null;
        $totalNgn       = $pending->total_ngn;
        $custEmail      = $pending->customer_email ?? ($paystackData['customer']['email'] ?? null);
        $resolvedUserId = $pending->user_id        ?? $userId;

        $pending->update(['payment_confirmed_at' => now(), 'recovery_error' => null]);

        // 6. Create order in a single transaction that also marks the checkout
        //    fulfilled and links the transaction record — all or nothing.
        $created = false;
        try {
            $order = DB::transaction(function () use (
                $reference, $resolvedUserId, $totalNgn, $fulfillment,
                $items, $couponData, $custEmail, $pending, &$created
            ) {
                $pending = PendingCheckout::whereKey($pending->id)->lockForUpdate()->firstOrFail();
                if ($pending->fulfilled_at) {
                    return Order::where('reference', $reference)->firstOrFail();
                }
                $created = true;
                $order = Order::create([
                    'user_id'                => $resolvedUserId,
                    'status'                 => 'paid',
                    'total'                  => $totalNgn,
                    'total_usd'              => 0,
                    'payment_method'         => 'paystack',
                    'payment_id'             => $reference,
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
                        // ⚠️ DO NOT REMOVE these two lines. Installation options must be
                        //    carried from the PendingCheckout snapshot onto the order item.
                        //    They are computed & validated in OrderController::saveCheckout
                        //    and stored in $pending->items; dropping them here means paid
                        //    orders lose their installation choice. This regressed once before.
                        'installation_option'    => $item['installation_option']    ?? null,
                        'installation_extra_ngn' => $item['installation_extra_ngn'] ?? 0,
                    ]);
                }

                // Remove the purchased items from inventory, atomically with the
                // order. Product rows are locked; oversell is allowed and flagged.
                $order->deductStock();

                if (!empty($couponData['id'])) {
                    $coupon = Coupon::lockForUpdate()->find($couponData['id']);
                    if ($coupon) {
                        // Re-check limit after acquiring the row lock — prevents concurrent
                        // over-redemption where two transactions both pass the pre-save check.
                        if ($coupon->validate((float) $pending->total_ngn + ($couponData['discount_ngn'] ?? 0), (int) $resolvedUserId)) {
                            throw new \RuntimeException('Coupon eligibility changed. Payment requires reconciliation.');
                        }
                        {
                            $coupon->increment('used_count');
                            CouponUsage::create([
                                'coupon_id' => $coupon->id,
                                'user_id'   => $resolvedUserId,
                                'order_id'  => $order->id,
                            ]);
                        }
                    }
                }

                // Mark checkout fulfilled and link the Paystack transaction inside
                // the same transaction so these writes are atomic with order creation.
                $pending->update(['fulfilled_at' => now()]);

                PaystackTransaction::where('reference', $reference)
                    ->whereNull('order_id')
                    ->update(['order_id' => $order->id]);

                return $order;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Distinguish duplicate-key from every other integrity violation.
            // MySQL error 1062 = "Duplicate entry"; PostgreSQL SQLSTATE 23505 = unique_violation.
            // SQLSTATE 23000 is intentionally NOT used — on MySQL it covers FK failures too.
            $mysqlDuplicate    = ($e->errorInfo[1] ?? null) === 1062;
            $postgresDuplicate = ($e->errorInfo[0] ?? '') === '23505';

            if (!$mysqlDuplicate && !$postgresDuplicate) {
                Log::error("Order creation DB error for ref={$reference}: " . $e->getMessage());
                return $this->result(false, null, null, false, 'transient', 'Payment confirmation is delayed. Keep your reference and try recovery again.');
            }

            // Re-fetch outside the aborted transaction — on PostgreSQL the connection
            // cannot run queries inside an error-state transaction.
            $existing = Order::where('reference', $reference)->first();
            if ($existing) {
                Log::info("Duplicate order caught via DB constraint for ref={$reference}");
                return $this->result(true, $existing->id, $existing->order_number, true, null, 'Order already exists.');
            }

            Log::error("Unique violation but order not found for ref={$reference}");
            return $this->result(false, null, null, false, 'transient', 'Unique constraint hit but order not found.');
        } catch (\Exception $e) {
            Log::error("Order creation failed for ref={$reference}: " . $e->getMessage());
            return $this->result(false, null, null, false, 'transient', 'Payment confirmation is delayed. Keep your reference and try recovery again.');
        }

        // 7. Send confirmation email after the transaction commits.
        //    Only reached on first creation — every duplicate path returns before step 6.
        if (!$created) return $this->result(true, $order->id, $order->order_number, true, null, 'Order already exists.');
        app(OrderNotificationService::class)->send($order, OrderConfirmation::class, 'confirmation');

        return $this->result(true, $order->id, $order->order_number, false, null, 'Order created.');
    }

    private function notifyAdminOfMissingCheckout(string $reference, array $paystackData): void
    {
        $adminEmail = config('mail.admin_address', config('mail.from.address'));
        if (!$adminEmail) {
            return;
        }
        try {
            $amountNgn    = number_format(($paystackData['amount'] ?? 0) / 100, 2);
            $customerEmail = $paystackData['customer']['email'] ?? 'unknown';
            Mail::raw(
                "URGENT: Paystack payment received but no checkout session found.\n\n" .
                "Reference: {$reference}\n" .
                "Amount: ₦{$amountNgn}\n" .
                "Customer: {$customerEmail}\n\n" .
                "The customer has paid but no order was created. Manual review and possible refund required.",
                fn ($m) => $m->to($adminEmail)->subject("ACTION REQUIRED: Paystack payment ref={$reference} has no order")
            );
        } catch (\Exception $e) {
            Log::error('Could not send missing-checkout admin alert: ' . $e->getMessage());
        }
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
