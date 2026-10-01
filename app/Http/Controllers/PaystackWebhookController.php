<?php

namespace App\Http\Controllers;

use App\Services\PaystackOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request, PaystackOrderService $service)
    {
        $rawBody = $request->getContent();
        $secret  = config('services.paystack.secret');

        // Verify HMAC-SHA512 over the raw body (not re-encoded JSON).
        $expected = hash_hmac('sha512', $rawBody, $secret);
        $received = $request->header('X-Paystack-Signature', '');

        if (!hash_equals($expected, $received)) {
            Log::warning('Paystack webhook: invalid signature');
            return response('Bad signature', 400);
        }

        $payload = json_decode($rawBody, true);
        $event   = $payload['event'] ?? '';

        if ($event !== 'charge.success') {
            // Log anything unexpected so we notice new event types in production.
            if ($event !== '') {
                Log::info("Paystack webhook: ignored event type '{$event}'");
            }
            return response('OK', 200);
        }

        $reference = $payload['data']['reference'] ?? null;

        if (!$reference) {
            Log::warning('Paystack webhook charge.success missing reference');
            return response('OK', 200);
        }

        $result = $service->fulfil($reference, null);

        Log::info('Paystack webhook fulfil result', [
            'reference'    => $reference,
            'success'      => $result['success'],
            'order_number' => $result['order_number'],
            'duplicate'    => $result['duplicate'],
            'error_type'   => $result['error_type'],
        ]);

        // Return 5xx for transient failures (DB down, Paystack API timeout) so
        // Paystack retries the webhook. Return 200 for permanent outcomes (fraud,
        // missing cart data, duplicates) — retrying will not change the result.
        if (!$result['success'] && $result['error_type'] === 'transient') {
            return response('Service unavailable', 503);
        }

        return response('OK', 200);
    }
}
