<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\PaystackTransaction;
use App\Models\User;
use App\Services\PaystackOrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PaystackOrderFulfillmentTest extends TestCase
{
    use DatabaseTransactions;

    private PaystackOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaystackOrderService::class);
        Mail::fake();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function mockPaystack(string $reference, int $amountKobo = 5000000, string $currency = 'NGN', string $status = 'success'): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status'  => true,
                'message' => 'Verification successful',
                'data'    => [
                    'id'        => 123456789,
                    'reference' => $reference,
                    'status'    => $status,
                    'amount'    => $amountKobo,
                    'currency'  => $currency,
                    'customer'  => ['email' => 'customer@example.com'],
                ],
            ], 200),
        ]);
    }

    private function mockPaystackFailed(string $reference): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status'  => false,
                'message' => 'Transaction reference not found',
                'data'    => ['reference' => $reference, 'status' => 'failed'],
            ], 400),
        ]);
    }

    private function makePending(string $reference, int $totalNgn = 50000, ?array $overrides = []): PendingCheckout
    {
        return PendingCheckout::create(array_merge([
            'reference'      => $reference,
            'user_id'        => null,
            'customer_email' => 'buyer@test.com',
            'total_ngn'      => $totalNgn,
            'items'          => [[
                'id'                     => 1,
                'name'                   => 'Samsung 55" TV',
                'basePriceNgn'           => $totalNgn,
                'effective_price_ngn'    => $totalNgn,
                'quantity'               => 1,
                'image'                  => null,
                'sku'                    => 'TV-001',
                'installation_option'    => null,
                'installation_extra_ngn' => 0,
            ]],
            'fulfillment'    => ['method' => 'pickup', 'pickup_location' => 'Enugu HQ'],
            'coupon'         => null,
        ], $overrides));
    }

    private function makeCoupon(array $overrides = []): Coupon
    {
        return Coupon::create(array_merge([
            'code'          => 'FULFIL' . strtoupper(uniqid()),
            'discount_type' => 'fixed',
            'value'         => 5000,
            'is_active'     => true,
            'used_count'    => 0,
        ], $overrides));
    }

    // ── Happy path ────────────────────────────────────────────────────────────

    /** @test */
    public function it_creates_an_order_when_paystack_verifies_successfully()
    {
        $ref = 'ps_test_' . uniqid();
        $this->makePending($ref, 50000);
        $this->mockPaystack($ref, 5000000); // ₦50,000 = 5,000,000 kobo

        $result = $this->service->fulfil($ref, null);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertFalse($result['duplicate']);
        $this->assertNotNull($result['order_id']);

        $this->assertDatabaseHas('orders', [
            'reference'      => $ref,
            'status'         => 'paid',
            'payment_method' => 'paystack',
        ]);
        $this->assertDatabaseHas('paystack_transactions', ['reference' => $ref, 'status' => 'success']);
    }

    /** @test */
    public function it_writes_order_items_from_pending_checkout()
    {
        $ref = 'ps_items_' . uniqid();
        $this->makePending($ref, 50000);
        $this->mockPaystack($ref, 5000000);

        $result = $this->service->fulfil($ref, null);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $result['order_id'],
            'name'     => 'Samsung 55" TV',
            'price'    => 50000,
            'quantity' => 1,
        ]);
    }

    /** @test */
    public function it_marks_pending_checkout_fulfilled_inside_the_transaction()
    {
        $ref = 'ps_mark_' . uniqid();
        $this->makePending($ref, 30000);
        $this->mockPaystack($ref, 3000000);

        $this->service->fulfil($ref, null);

        $this->assertNotNull(
            PendingCheckout::where('reference', $ref)->value('fulfilled_at'),
            'fulfilled_at should be set after order creation.'
        );
    }

    /** @test */
    public function it_links_paystack_transaction_to_the_created_order()
    {
        $ref = 'ps_link_' . uniqid();
        $this->makePending($ref, 50000);
        $this->mockPaystack($ref, 5000000);

        $result = $this->service->fulfil($ref, null);

        $this->assertDatabaseHas('paystack_transactions', [
            'reference' => $ref,
            'order_id'  => $result['order_id'],
        ]);
    }

    /** @test */
    public function it_sends_confirmation_email_on_first_creation_only()
    {
        $ref = 'ps_email_' . uniqid();
        $this->makePending($ref, 50000);
        $this->mockPaystack($ref, 5000000);

        $this->service->fulfil($ref, null);

        Mail::assertSent(OrderConfirmation::class, 1);

        // Second call (duplicate) must not send another email.
        $this->mockPaystack($ref, 5000000);
        $this->service->fulfil($ref, null);

        Mail::assertSent(OrderConfirmation::class, 1);
    }

    /** @test */
    public function it_returns_duplicate_true_and_same_order_id_on_second_call()
    {
        $ref = 'ps_dup_' . uniqid();
        $this->makePending($ref, 50000);
        $this->mockPaystack($ref, 5000000);

        $first = $this->service->fulfil($ref, null);
        $this->assertFalse($first['duplicate']);

        $this->mockPaystack($ref, 5000000);
        $second = $this->service->fulfil($ref, null);

        $this->assertTrue($second['success']);
        $this->assertTrue($second['duplicate']);
        $this->assertEquals($first['order_id'], $second['order_id']);
        $this->assertEquals(1, Order::where('reference', $ref)->count());
    }

    /** @test */
    public function it_returns_failure_when_paystack_verification_fails()
    {
        $ref = 'ps_bad_' . uniqid();
        $this->mockPaystackFailed($ref);

        $result = $this->service->fulfil($ref, null);

        $this->assertFalse($result['success']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    // ── Coupon redemption on fulfilment ────────────────────────────────────────

    /** @test */
    public function it_redeems_a_coupon_once_on_fulfilment()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['max_uses' => 10, 'used_count' => 0]);
        $ref    = 'ps_coupon_' . uniqid();

        $this->makePending($ref, 45000, [
            'user_id' => $user->id,
            'coupon'  => ['id' => $coupon->id, 'code' => $coupon->code, 'discount_ngn' => 5000],
        ]);
        $this->mockPaystack($ref, 4500000); // ₦45,000

        $result = $this->service->fulfil($ref, $user->id);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertEquals(1, $coupon->fresh()->used_count);
        $this->assertDatabaseHas('coupon_usages', [
            'coupon_id' => $coupon->id,
            'user_id'   => $user->id,
            'order_id'  => $result['order_id'],
        ]);
        $this->assertDatabaseHas('orders', [
            'reference'           => $ref,
            'coupon_id'           => $coupon->id,
            'coupon_code_used'    => $coupon->code,
            'coupon_discount_ngn' => 5000,
        ]);
    }

    /** @test */
    public function it_does_not_redeem_the_coupon_again_on_a_duplicate_fulfilment()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['max_uses' => 10, 'used_count' => 0]);
        $ref    = 'ps_coupondup_' . uniqid();

        $this->makePending($ref, 45000, [
            'user_id' => $user->id,
            'coupon'  => ['id' => $coupon->id, 'code' => $coupon->code, 'discount_ngn' => 5000],
        ]);
        $this->mockPaystack($ref, 4500000);
        $this->service->fulfil($ref, $user->id);

        // Idempotent second call (browser + webhook race) must not double-count.
        $this->mockPaystack($ref, 4500000);
        $second = $this->service->fulfil($ref, $user->id);

        $this->assertTrue($second['duplicate']);
        $this->assertEquals(1, $coupon->fresh()->used_count);
        $this->assertEquals(1, CouponUsage::where('coupon_id', $coupon->id)->count());
    }

    /** @test */
    public function it_creates_the_order_but_skips_a_coupon_already_at_its_limit()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['max_uses' => 3, 'used_count' => 3]); // already exhausted
        $ref    = 'ps_couponmax_' . uniqid();

        $this->makePending($ref, 45000, [
            'user_id' => $user->id,
            'coupon'  => ['id' => $coupon->id, 'code' => $coupon->code, 'discount_ngn' => 5000],
        ]);
        $this->mockPaystack($ref, 4500000);

        $result = $this->service->fulfil($ref, $user->id);

        // Payment already succeeded, so the customer still gets their order...
        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('orders', ['reference' => $ref]);
        // ...but the exhausted coupon must not be over-redeemed past its cap.
        $this->assertEquals(3, $coupon->fresh()->used_count);
        $this->assertEquals(0, CouponUsage::where('coupon_id', $coupon->id)->count());
    }

    // ── Security tests ────────────────────────────────────────────────────────

    /** @test */
    public function it_rejects_underpayment()
    {
        $ref = 'ps_under_' . uniqid();
        $this->makePending($ref, 50000);           // expects ₦50,000 = 5,000,000 kobo
        $this->mockPaystack($ref, 10000);          // attacker paid ₦100 = 10,000 kobo

        $result = $this->service->fulfil($ref, null);

        $this->assertFalse($result['success']);
        $this->assertSame('fraud', $result['error_type']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_rejects_wrong_currency()
    {
        $ref = 'ps_curr_' . uniqid();
        $this->makePending($ref, 50000);
        $this->mockPaystack($ref, 5000000, 'USD'); // USD instead of NGN

        $result = $this->service->fulfil($ref, null);

        $this->assertFalse($result['success']);
        $this->assertSame('fraud', $result['error_type']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_rejects_when_no_pending_checkout_exists()
    {
        $ref = 'ps_nopending_' . uniqid();
        // No PendingCheckout created — simulates a tampered or missing pre-save.
        $this->mockPaystack($ref, 10000); // ₦100

        $result = $this->service->fulfil($ref, null);

        $this->assertFalse($result['success']);
        $this->assertNull($result['error_type']); // permanent — no retry
        $this->assertStringContainsString($ref, $result['message']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function webhook_returns_503_on_transient_error()
    {
        $ref = 'ps_trans_' . uniqid();
        $this->makePending($ref, 50000);

        // Simulate a Paystack API timeout on both retries.
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::sequence()
                ->push(['error' => 'timeout'], 500)
                ->push(['error' => 'timeout'], 500),
        ]);

        $body      = json_encode(['event' => 'charge.success', 'data' => ['reference' => $ref]]);
        $signature = hash_hmac('sha512', $body, config('services.paystack.secret', 'test-secret'));

        // Use call() with the raw body so $request->getContent() matches what we signed.
        $response = $this->call(
            'POST',
            '/webhooks/paystack',
            [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_PAYSTACK_SIGNATURE' => $signature],
            $body
        );

        // Paystack retries on 5xx — verify the webhook signals that correctly.
        $this->assertSame(503, $response->getStatusCode());
    }
}
