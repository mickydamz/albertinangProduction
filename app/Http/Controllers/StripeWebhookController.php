<?php

namespace App\Http\Controllers;

use App\Services\StripeOrderService;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, StripeOrderService $service)
    {
        $secret = config('services.stripe.webhook_secret');
        if (!$secret) return response('Webhook unavailable', 503);
        try {
            $event = \Stripe\Webhook::constructEvent($request->getContent(), $request->header('Stripe-Signature', ''), $secret);
        } catch (\Throwable $e) {
            return response('Invalid signature', 400);
        }
        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $reference = $intent->metadata->reference ?? null;
            if (!$reference) return response('Missing checkout reference', 400);
            $result = $service->fulfil($reference, $intent->id, null);
            return response('OK', !$result['success'] && $result['error_type'] === 'transient' ? 503 : 200);
        }
        if (in_array($event->type, ['refund.updated', 'refund.created', 'refund.failed'], true)) {
            $attempt = \App\Models\RefundAttempt::where('gateway', 'stripe')->where('gateway_id', $event->data->object->id)->first();
            if ($attempt) app(\App\Services\RefundService::class)->reconcile($attempt);
        }
        return response('OK');
    }
}
