<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminInvoiceSettingsController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PendingCheckout;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\Storage;
use App\Models\OrderCancellation;
use App\Mail\OrderCancellationRequestedUser;
use App\Mail\OrderCancellationRequestedAdmin;
use App\Services\PaystackOrderService;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class OrderController extends Controller
{
    /**
     * Persist the cart server-side before the Paystack popup opens.
     *
     * Accepts only product IDs, quantities, location IDs, and a coupon code.
     * All prices, fees, and names are computed from the database.
     * The reference is generated here and returned — the browser never picks it.
     */
    public function saveCheckout(Request $request)
    {
        $validated = $request->validate([
            'customer_email'                   => 'nullable|email|max:255',
            'items'                            => 'required|array|min:1',
            'items.*.product_id'               => 'required|integer|exists:products,id',
            'items.*.quantity'                 => 'required|integer|min:1|max:100',
            'items.*.installation_option'      => 'nullable|string|max:255',
            'coupon_code'                      => 'nullable|string|max:64',
            'fulfillment'                      => 'nullable|array',
            'fulfillment.method'               => 'nullable|string|in:pickup,delivery',
            'fulfillment.pickup_point_id'      => 'nullable|integer',
            'fulfillment.pickup_location'      => 'nullable|string|max:255',
            'fulfillment.delivery_location_id' => 'nullable|integer',
            'fulfillment.shipping_address'     => 'nullable|string|max:500',
        ]);

        // 1. Load active products in one query. Inactive/unlisted products are rejected.
        $productIds = array_column($validated['items'], 'product_id');
        $products   = Product::active()->with(['category', 'Subcategory'])
            ->whereIn('id', $productIds)->get()->keyBy('id');

        // 2. Compute item snapshots using integer kobo arithmetic to avoid float drift.
        $computedItems = [];
        $subtotalKobo  = 0;
        $requiresTruck = false;
        $totalWeightKg = 0.0;

        foreach ($validated['items'] as $row) {
            $product = $products->get($row['product_id']);
            if (!$product) {
                return response()->json(['error' => 'Product ' . $row['product_id'] . ' is not available for purchase.'], 422);
            }

            if ($product->requires_truck) {
                $requiresTruck = true;
            }

            $basePriceKobo = (int) round((float) $product->sell_price * 100);
            $qty           = (int) $row['quantity'];

            // Trickle-down weight: product → subcategory → category → 5 kg default.
            // Mirrors ProductController::truckCheck so the server reaches the same
            // truck decision the browser showed the customer.
            $unitWeightKg = $product->weight_kg
                ?? $product->Subcategory?->estimated_weight_kg
                ?? $product->category?->estimated_weight_kg
                ?? 5.0;
            $totalWeightKg += (float) $unitWeightKg * $qty;
            $optionLabel   = $row['installation_option'] ?? null;
            $extraKobo     = 0;

            if ($optionLabel !== null) {
                $matched = false;
                if (is_array($product->installation_options)) {
                    foreach ($product->installation_options as $opt) {
                        if (is_array($opt) && ($opt['label'] ?? '') === $optionLabel) {
                            $extraKobo = (int) round((float) ($opt['price'] ?? 0) * 100);
                            $matched   = true;
                            break;
                        }
                    }
                }
                if (!$matched) {
                    return response()->json([
                        'error' => "Installation option '{$optionLabel}' is not valid for '{$product->name}'.",
                    ], 422);
                }
            }

            $effectivePriceKobo  = $basePriceKobo + $extraKobo;
            $subtotalKobo       += $effectivePriceKobo * $qty;

            $computedItems[] = [
                'id'                     => $product->id,
                'name'                   => $product->name,
                'basePriceNgn'           => $basePriceKobo / 100,
                'effective_price_ngn'    => $effectivePriceKobo / 100,
                'quantity'               => $qty,
                'image'                  => $product->image ?? null,
                'sku'                    => $product->sku   ?? null,
                // ⚠️ DO NOT REMOVE. This snapshot IS the order — PaystackOrderService
                //    creates the order straight from PendingCheckout->items. The
                //    installation label/extra computed and validated above must be
                //    persisted here or the customer's installation choice is lost.
                'installation_option'    => $optionLabel,
                'installation_extra_ngn' => (int) ($extraKobo / 100),
            ];
        }

        // 2b. Apply the weight and order-value thresholds server-side. These mirror
        //     the browser's checks (checkout.blade.php) so a cart that triggers the
        //     truck rate by total weight or order value is charged the truck fee,
        //     not just carts holding an explicitly flagged product.
        $weightThresholdKg      = (float) \App\Models\Setting::get('truck_weight_threshold_kg', 30);
        $orderValueThresholdNgn = (float) \App\Models\Setting::get('truck_order_value_threshold_ngn', 1000000);

        if ($totalWeightKg >= $weightThresholdKg) {
            $requiresTruck = true;
        }
        if (($subtotalKobo / 100) >= $orderValueThresholdNgn) {
            $requiresTruck = true;
        }

        // 3. Coupon server-side validation and discount calculation.
        $couponData  = null;
        $couponError = null;
        $couponCode  = $validated['coupon_code'] ?? null;
        $subtotalNgn = $subtotalKobo / 100;

        if ($couponCode) {
            $coupon = Coupon::active()->where('code', $couponCode)->first();
            if ($coupon) {
                $couponValidationError = $coupon->validate($subtotalNgn, Auth::id() ?? 0);
                if ($couponValidationError === null) {
                    $couponData = [
                        'id'           => $coupon->id,
                        'code'         => $coupon->code,
                        'discount_ngn' => $coupon->calculateDiscount($subtotalNgn),
                    ];
                } else {
                    $couponError = $couponValidationError;
                    Log::info("saveCheckout: coupon '{$couponCode}' rejected — {$couponError}");
                }
            } else {
                $couponError = 'Coupon not found or expired.';
            }
        }

        // 4. Delivery fee and all display names resolved from the database.
        //    The browser sends only IDs; we never trust client-supplied strings or fees.
        $fulfillment     = $validated['fulfillment'] ?? [];
        $method          = $fulfillment['method']    ?? 'pickup';
        $deliveryFeeKobo = 0;
        $resolvedFulfillment = ['method' => $method];

        if ($method === 'delivery') {
            $locationId = $fulfillment['delivery_location_id'] ?? null;
            if ($locationId) {
                $location = \App\Models\Location::with('state')->find($locationId);
                if (!$location || !$location->is_active) {
                    return response()->json(['error' => 'Selected delivery location is not available.'], 422);
                }
                $deliveryFeeKobo = $requiresTruck
                    ? (int) round((float) $location->truck_shipping_cost * 100)
                    : (int) round((float) $location->shipping_cost * 100);

                $resolvedFulfillment['delivery_location_id']   = $location->id;
                $resolvedFulfillment['delivery_location_name'] = $location->name;
                $resolvedFulfillment['delivery_state_id']      = $location->state_id;
                $resolvedFulfillment['delivery_state_name']    = $location->state?->name;
                $resolvedFulfillment['delivery_fee_ngn']       = $deliveryFeeKobo / 100;
                $resolvedFulfillment['shipping_address']       = $fulfillment['shipping_address'] ?? null;

                // Note: the checkout delivery address is stored on the order only.
                // The customer's profile address is managed from the account page
                // and is intentionally NOT overwritten from checkout.
            }
        } else {
            $pickupPointId = $fulfillment['pickup_point_id'] ?? null;
            if ($pickupPointId) {
                $point = \App\Models\PickupPoint::with('location')->find($pickupPointId);
                if ($point) {
                    $resolvedFulfillment['pickup_point_id']      = $point->id;
                    $resolvedFulfillment['pickup_point_name']    = $point->name;
                    $resolvedFulfillment['pickup_point_address'] = $point->address;
                    $resolvedFulfillment['pickup_location_id']   = $point->location_id;
                    $resolvedFulfillment['pickup_location']      = $point->location?->name;
                }
            }
            $resolvedFulfillment['delivery_fee_ngn'] = 0;
            // Fall back to a plain string for pages that don't use pickup-point IDs.
            if (!isset($resolvedFulfillment['pickup_location'])) {
                $resolvedFulfillment['pickup_location'] = $fulfillment['pickup_location'] ?? null;
            }
        }

        // 5. Total in integer kobo — coupon discount and delivery fee applied.
        $couponDiscountKobo = (int) round((float) ($couponData['discount_ngn'] ?? 0) * 100);
        $totalKobo          = max(0, $subtotalKobo - $couponDiscountKobo + $deliveryFeeKobo);

        // 6. Reference generated here — the browser never chooses it.
        $reference = 'ps_' . \Illuminate\Support\Str::ulid();

        PendingCheckout::create([
            'reference'      => $reference,
            'user_id'        => Auth::id(),
            'customer_email' => $validated['customer_email'] ?? Auth::user()?->email,
            'total_ngn'      => $totalKobo / 100,
            'items'          => $computedItems,
            'fulfillment'    => $resolvedFulfillment,
            'coupon'         => $couponData,
        ]);

        // TEMP DIAGNOSTIC: records what we hand to the Paystack popup on every
        // attempt. Lets us confirm the amount/currency/reference are sane while
        // debugging "Unable to process transaction". Remove once resolved.
        Log::info('saveCheckout prepared checkout', [
            'reference'   => $reference,
            'total_ngn'   => $totalKobo / 100,
            'amount_kobo' => $totalKobo,
            'currency'    => 'NGN',
            'items'       => count($computedItems),
            'has_email'   => !empty($validated['customer_email'] ?? Auth::user()?->email),
            'user_id'     => Auth::id(),
        ]);

        return response()->json([
            'saved'        => true,
            'reference'    => $reference,
            'total_ngn'    => $totalKobo / 100,
            'coupon'       => $couponData
                ? ['code' => $couponData['code'], 'discount_ngn' => $couponData['discount_ngn']]
                : null,
            'coupon_error' => $couponError,
        ]);
    }

    /**
     * Confirm a Stripe card payment and create the order.
     *
     * Mirrors the Paystack confirm path: the browser sends ONLY the checkout
     * reference and the PaymentIntent id. StripeOrderService verifies the payment
     * with Stripe and builds the order from the trusted PendingCheckout snapshot —
     * no prices, totals, or item data are taken from the request.
     */
    public function storeStripeOrder(Request $request, \App\Services\StripeOrderService $service)
    {
        $validated = $request->validate([
            'reference'         => 'required|string|max:255',
            'payment_intent_id' => 'required|string|max:255',
        ]);

        $result = $service->fulfil($validated['reference'], $validated['payment_intent_id'], Auth::id());

        if (!$result['success'] && !$result['duplicate']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Could not confirm your order. Your card was charged — contact support with reference: ' . $validated['reference'],
            ], 400);
        }

        return response()->json([
            'success'      => true,
            'order_id'     => $result['order_id'],
            'order_number' => $result['order_number'],
            'reference'    => $validated['reference'],
            'duplicate'    => $result['duplicate'],
        ]);
    }

    public function index()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->with(['items', 'return', 'cancellation'])
            ->latest()
            ->paginate(10);

        $orderCount = $orders->total();

        return view('sims.orders', compact('orders', 'orderCount'));
    }

    /**
     * Guard: the current user may only act on their own orders. Account order
     * pages are route-model-bound, so without this any logged-in user could
     * read or mutate another customer's order by guessing its id (IDOR).
     */
    private function authorizeOrderOwner(Order $order): void
    {
        if ($order->user_id === null || $order->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function invoice(Order $order)
    {
        $this->authorizeOrderOwner($order);
        $order->load('items', 'user');
        $tpl = AdminInvoiceSettingsController::templateVars($order->fulfillment_method);
        return view('sims.invoice', array_merge(compact('order'), $tpl));
    }

    public function downloadInvoice(Order $order)
    {
        $this->authorizeOrderOwner($order);
        $order->load('items', 'user');
        $tpl = AdminInvoiceSettingsController::templateVars($order->fulfillment_method);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sims.invoice-pdf', array_merge(compact('order'), $tpl));

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'dpi'                  => 96,
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'chroot'               => public_path(),
        ]);

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    public function verifyPaystack(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return response()->json(['status' => 'failed', 'message' => 'No reference provided.'], 400);
        }

        try {
            $response = Http::withToken(config('services.paystack.secret'))
                ->get('https://api.paystack.co/transaction/verify/' . $reference);

            $data = $response->json();

            if ($response->successful() && ($data['data']['status'] ?? '') === 'success') {
                return response()->json(['status' => 'success', 'data' => $data['data']]);
            }

            return response()->json([
                'status'  => 'failed',
                'message' => $data['message'] ?? 'Verification failed.',
            ], 400);

        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function showReturn(Order $order)
    {
        $this->authorizeOrderOwner($order);
        $existingReturn = $order->return;
        return view('sims.return', compact('order', 'existingReturn'));
    }

    public function submitReturn(Request $request, Order $order)
    {
        $this->authorizeOrderOwner($order);

        $validated = $request->validate([
            'reason'   => 'required|string|min:20|max:1000',
            'evidence' => 'nullable|image|max:4096',
        ]);

        $path = null;
        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('returns', 'public');
        }

        OrderReturn::create([
            'order_id'      => $order->id,
            'user_id'       => auth()->id(),
            'reason'        => $validated['reason'],
            'evidence_path' => $path,
            'status'        => 'pending',
        ]);

        return redirect()->route('account.orders')
            ->with('success', 'Return request submitted. We\'ll review it within 2 business days.');
    }

    public function showCancel(Order $order)
    {
        $this->authorizeOrderOwner($order);
        $existingCancellation = $order->cancellation;

        return view('sims.cancel', compact('order', 'existingCancellation'));
    }

    public function submitCancel(Request $request, Order $order)
    {
        $this->authorizeOrderOwner($order);

        if ($order->cancellation) {
            return back()->with('error', 'You have already submitted a cancellation request for this order.');
        }

        if (!in_array($order->status, ['paid', 'pending', 'processing'])) {
            return back()->with('error', 'This order cannot be cancelled at its current status.');
        }

        $request->validate([
            'reason' => 'required|string|min:20|max:1000',
        ]);

        $cancellation = OrderCancellation::create([
            'order_id' => $order->id,
            'user_id'  => auth()->id(),
            'reason'   => $request->reason,
            'status'   => 'pending',
        ]);

        $order->update(['status' => 'cancelled']);

        $refundResult = null;

        if ($order->payment_method === 'paystack') {
            $refundResult = $this->processPaystackRefund($order, $cancellation);
        } elseif ($order->payment_method === 'stripe') {
            $refundResult = $this->processStripeRefund($order, $cancellation);
        }

        if (!$refundResult || !$refundResult['success']) {
            $cancellation->update(['status' => 'approved']);
        }

        try {
            Mail::to($order->user->email)->send(
                new OrderCancellationRequestedUser($order, $cancellation)
            );
        } catch (\Exception $e) {
            \Log::error('Cancellation user email failed: ' . $e->getMessage());
        }

        try {
            Mail::to(config('mail.admin_address', 'orders@albertinang.com'))
                ->send(new OrderCancellationRequestedAdmin($order, $cancellation));
        } catch (\Exception $e) {
            \Log::error('Cancellation admin email failed: ' . $e->getMessage());
        }

        $message = 'Your order has been cancelled.';
        if ($refundResult && $refundResult['success']) {
            $message .= ' A refund has been initiated and will reflect in 5–10 business days.';
        } elseif (in_array($order->payment_method, ['paystack', 'stripe'])) {
            $message .= ' We could not process your refund automatically — our team will handle it manually.';
        }

        return back()->with('success', $message);
    }

    private function processPaystackRefund(Order $order, OrderCancellation $cancellation): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.paystack.secret'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.paystack.co/refund', [
                'transaction'   => $order->reference,
                'amount'        => (int) ($order->total * 100),
                'currency'      => 'NGN',
                'customer_note' => 'Refund for cancelled order #' . $order->order_number,
                'merchant_note' => 'Auto-refund on cancellation',
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false)) {
                $order->update(['status' => 'refunded']);

                $cancellation->update([
                    'status'        => 'refunded',
                    'refund_id'     => $data['data']['id']     ?? null,
                    'refund_status' => $data['data']['status'] ?? 'pending',
                    'refunded_at'   => now(),
                ]);

                \Log::info('Paystack refund initiated for order #' . $order->id, [
                    'refund_id' => $data['data']['id'] ?? null,
                ]);

                return ['success' => true, 'data' => $data['data']];
            }

            \Log::error('Paystack refund failed for order #' . $order->id, [
                'response' => $data,
            ]);

            return ['success' => false, 'message' => $data['message'] ?? 'Refund failed.'];

        } catch (\Exception $e) {
            \Log::error('Paystack refund exception for order #' . $order->id . ': ' . $e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function processStripeRefund(Order $order, OrderCancellation $cancellation): array
    {
        try {
            $paymentIntentId = $order->payment_id;

            if (empty($paymentIntentId) || !str_starts_with($paymentIntentId, 'pi_')) {
                \Log::error('Stripe refund skipped for order #' . $order->id . ': invalid or missing PaymentIntent ID.', [
                    'payment_id' => $paymentIntentId,
                ]);

                return ['success' => false, 'message' => 'No valid Stripe PaymentIntent found on this order.'];
            }

            $refund = $this->createStripeRefund($paymentIntentId, $order);

            if (in_array($refund->status, ['succeeded', 'pending'])) {
                $order->update(['status' => 'refunded']);

                $cancellation->update([
                    'status'        => 'refunded',
                    'refund_id'     => $refund->id,
                    'refund_status' => $refund->status,
                    'refunded_at'   => now(),
                ]);

                \Log::info('Stripe refund initiated for order #' . $order->id, [
                    'refund_id' => $refund->id,
                    'status'    => $refund->status,
                ]);

                return ['success' => true, 'data' => $refund];
            }

            \Log::error('Stripe refund did not succeed for order #' . $order->id, [
                'refund_id' => $refund->id ?? null,
                'status'    => $refund->status ?? null,
            ]);

            return ['success' => false, 'message' => 'Refund status: ' . ($refund->status ?? 'unknown')];

        } catch (ApiErrorException $e) {
            \Log::error('Stripe refund API error for order #' . $order->id . ': ' . $e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];

        } catch (\Exception $e) {
            \Log::error('Stripe refund exception for order #' . $order->id . ': ' . $e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Issue the actual Stripe refund via the SDK. Isolated so it can be stubbed
     * in tests without hitting the network (see StripeOrderService for the same
     * seam pattern). Returns the Stripe Refund object.
     */
    protected function createStripeRefund(string $paymentIntentId, Order $order)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        return $stripe->refunds->create([
            'payment_intent' => $paymentIntentId,
            'reason'         => 'requested_by_customer',
            'metadata'       => [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'context'      => 'Order cancellation auto-refund',
            ],
        ]);
    }

    /**
     * Browser-side Paystack confirmation endpoint.
     * All cart data comes from PendingCheckout (saved by saveCheckout before the popup).
     * This endpoint just passes the reference; fulfil() does the rest.
     */
    public function verifyAndStorePaystackOrder(Request $request, PaystackOrderService $service)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        $result = $service->fulfil($validated['reference'], Auth::id());

        if (!$result['success'] && !$result['duplicate']) {
            return response()->json([
                'success' => false,
                'status'  => 'failed',
                'message' => $result['message'] ?? 'Could not confirm your order. Your payment was received — contact support with reference: ' . $validated['reference'],
            ], 400);
        }

        return response()->json([
            'success'      => true,
            'status'       => 'success',
            'order_id'     => $result['order_id'],
            'order_number' => $result['order_number'],
            'reference'    => $validated['reference'],
            'duplicate'    => $result['duplicate'],
        ]);
    }

    public function guestDownloadInvoice(\App\Models\Order $order)
    {
        $order->load('items', 'user');
        $tpl = AdminInvoiceSettingsController::templateVars($order->fulfillment_method);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sims.invoice-pdf', array_merge(compact('order'), $tpl));

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'dpi'                  => 96,
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'chroot'               => public_path(),
        ]);

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}