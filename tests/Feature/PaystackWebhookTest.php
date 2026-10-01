<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Services\PaystackOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Tests for the Paystack webhook endpoint (/webhooks/paystack).
 *
 * Paystack retries on non-2xx — the endpoint must ALWAYS return 200.
 * Signature validation must reject tampered payloads.
 * charge.success must create an order via PaystackOrderService.
 */
class PaystackWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        // Ensure a secret is available for HMAC generation in tests
        config(['services.paystack.secret' => 'test_webhook_secret_key']);
    }

    private function webhookPayload(string $reference, string $event = 'charge.success'): array
    {
        return [
            'event' => $event,
            'data'  => [
                'id'        => 999888777,
                'reference' => $reference,
                'status'    => 'success',
                'amount'    => 50000,
                'currency'  => 'NGN',
                'customer'  => ['email' => 'webhook@test.com'],
            ],
        ];
    }

    private function signedRequest(array $payload): array
    {
        $body      = json_encode($payload);
        $signature = hash_hmac('sha512', $body, config('services.paystack.secret'));
        return [$body, $signature];
    }

    /**
     * POST a raw body + signature to the webhook.
     *
     * The signature MUST travel in the server array (HTTP_X_PAYSTACK_SIGNATURE),
     * not via withHeaders(): when ->call() is given an explicit server array the
     * withHeaders() values are dropped, so the endpoint would see an empty
     * signature and reject every request as tampered.
     */
    private function postWebhook(string $body, string $signature)
    {
        return $this->call(
            'POST',
            '/webhooks/paystack',
            [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_PAYSTACK_SIGNATURE' => $signature],
            $body
        );
    }

    /** @test */
    public function it_rejects_webhook_with_invalid_signature()
    {
        $ref     = 'ps_wh_badsig_' . uniqid();
        $payload = $this->webhookPayload($ref);
        $body    = json_encode($payload);

        $response = $this->postWebhook($body, 'invalid_signature_xxxx');

        $response->assertStatus(400);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_always_returns_200_for_valid_signature()
    {
        $ref             = 'ps_wh_200_' . uniqid();
        $payload         = $this->webhookPayload($ref);
        [$body, $sig]    = $this->signedRequest($payload);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data'   => ['reference' => $ref, 'status' => 'success', 'amount' => 50000, 'currency' => 'NGN', 'customer' => ['email' => 'x@x.com']],
            ], 200),
        ]);

        // No PendingCheckout → order creation will fail (no cart data), but the
        // HTTP response must still be 200 so Paystack stops retrying.
        $response = $this->postWebhook($body, $sig);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_ignores_non_charge_success_events()
    {
        $ref          = 'ps_wh_other_' . uniqid();
        $payload      = $this->webhookPayload($ref, 'transfer.success');
        [$body, $sig] = $this->signedRequest($payload);

        $response = $this->postWebhook($body, $sig);

        $response->assertStatus(200)->assertSee('OK');
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_creates_order_from_pending_checkout_on_charge_success()
    {
        $ref = 'ps_wh_ok_' . uniqid();

        PendingCheckout::create([
            'reference'      => $ref,
            'user_id'        => null,
            'customer_email' => 'webhook@test.com',
            'total_ngn'      => 50000,
            'items'          => [[
                'id' => 5, 'name' => 'Microwave', 'effective_price_ngn' => 50000,
                'basePriceNgn' => 50000, 'quantity' => 1,
                'image' => null, 'sku' => null,
                'installation_option' => null, 'installation_extra_ngn' => 0,
            ]],
            'fulfillment' => ['method' => 'pickup', 'pickup_location' => 'Enugu HQ'],
            'coupon'      => null,
        ]);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data'   => [
                    'id' => 999, 'reference' => $ref, 'status' => 'success',
                    'amount' => 5000000, 'currency' => 'NGN', 'customer' => ['email' => 'webhook@test.com'],
                ],
            ], 200),
        ]);

        $payload      = $this->webhookPayload($ref);
        [$body, $sig] = $this->signedRequest($payload);

        $response = $this->postWebhook($body, $sig);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', ['reference' => $ref, 'status' => 'paid']);
        $this->assertDatabaseHas('order_items', ['name' => 'Microwave']);
    }

    /** @test */
    public function it_does_not_double_create_order_when_webhook_fires_after_browser_confirm()
    {
        $ref = 'ps_wh_nodup_' . uniqid();

        // Simulate: browser confirm-order already created the order
        Order::create([
            'reference'      => $ref,
            'status'         => 'paid',
            'total'          => 50000,
            'total_usd'      => 0,
            'payment_method' => 'paystack',
            'payment_id'     => $ref,
        ]);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data'   => ['reference' => $ref, 'status' => 'success', 'amount' => 5000000, 'currency' => 'NGN', 'customer' => ['email' => 'x@x.com']],
            ], 200),
        ]);

        $payload      = $this->webhookPayload($ref);
        [$body, $sig] = $this->signedRequest($payload);

        $this->postWebhook($body, $sig);

        // Must still be exactly one order
        $this->assertEquals(1, Order::where('reference', $ref)->count());
    }
}
