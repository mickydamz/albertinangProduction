<?php

namespace App\Http\Controllers;

use App\Models\PendingCheckout;
use App\Models\Order;
use App\Models\RefundAttempt;
use App\Services\PaystackOrderService;
use App\Services\StripeOrderService;
use App\Services\RefundService;
use Illuminate\Http\Request;

class CheckoutRecoveryController extends Controller
{
    public function show(PendingCheckout $checkout)
    {
        abort_unless((int) $checkout->user_id === (int) auth()->id(), 403);
        $order = Order::where('reference', $checkout->reference)->first();
        return view('checkout-recovery', compact('checkout', 'order'));
    }

    public function adminRecover(PendingCheckout $checkout)
    {
        $result = $checkout->gateway === 'stripe'
            ? app(StripeOrderService::class)->fulfil($checkout->reference, $checkout->payment_intent_id ?? '', null)
            : app(PaystackOrderService::class)->fulfil($checkout->reference, null);
        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function recover(PendingCheckout $checkout)
    {
        abort_unless((int) $checkout->user_id === (int) auth()->id(), 403);
        $result = $checkout->gateway === 'stripe'
            ? app(StripeOrderService::class)->fulfil($checkout->reference, $checkout->payment_intent_id ?? '', auth()->id())
            : app(PaystackOrderService::class)->fulfil($checkout->reference, auth()->id());
        if (request()->expectsJson()) return response()->json($result, $result['success'] ? 200 : 202);
        return back()->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Order created.' : 'Confirmation is delayed. Keep this reference and try again.');
    }

    public function refund(Request $request, Order $order)
    {
        $data = $request->validate(['amount' => 'required|numeric|min:0.01', 'request_key' => 'required|uuid']);
        $attempt = app(RefundService::class)->request($order, round((float) $data['amount'], 2), $data['request_key']);
        return back()->with('success', 'Refund reference ' . $attempt->request_key . ': ' . $attempt->status);
    }

    public function reconcileRefund(RefundAttempt $refund)
    {
        app(RefundService::class)->reconcile($refund);
        return back()->with('success', 'Refund status checked with the gateway.');
    }
}
