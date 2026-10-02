<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\RefundAttempt;
use App\Models\User;
use App\Services\RefundService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Direct coverage for App\Services\RefundService — the single place that talks to
 * the gateways and maps a refund to a financial status. Exercises full/partial
 * refunds, idempotency (request_key reuse), over-refund guards, gateway rejection
 * and gateway timeout. No request ever leaves the machine (Http::fake).
 */
class RefundServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function service(): RefundService
    {
        return app(RefundService::class);
    }

    private function makeOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'user_id'        => User::factory()->create(['email' => 'buyer_' . uniqid() . '@test.com'])->id,
            'status'         => 'delivered',
            'total'          => 50000,
            'total_usd'      => 32.50,
            'payment_method' => 'paystack',
            'reference'      => 'ps_ref_' . uniqid(),
            'payment_id'     => 'pi_' . uniqid(),
            'customer_email' => 'buyer@test.com',
        ], $overrides));
    }

    private function fakePaystackRefund(array $data, int $status = 200): void
    {
        Http::fake(['api.paystack.co/refund' => Http::response(
            $status >= 400 && empty($data) ? ['status' => false, 'message' => 'Rejected'] : ['status' => true, 'data' => $data],
            $status
        )]);
    }

    // ── Happy paths ───────────────────────────────────────────────────────────

    /** @test */
    public function a_settled_paystack_refund_marks_the_order_refunded_with_evidence()
    {
        $this->fakePaystackRefund(['id' => 900001, 'status' => 'processed']);

        $order   = $this->makeOrder(['total' => 50000]);
        $attempt = $this->service()->request($order, 50000, (string) Str::uuid());

        $this->assertSame('completed', $attempt->status);
        $this->assertSame('900001', (string) $attempt->gateway_id);
        $this->assertSame('processed', $attempt->gateway_status);
        $this->assertNotNull($attempt->completed_at);

        $order->refresh();
        $this->assertSame('refunded', $order->status);
        $this->assertSame(50000.0, (float) $order->refund_amount);
        $this->assertSame('900001', (string) $order->refund_id);
        $this->assertNotNull($order->refunded_at);
        $this->assertNull($order->refund_failure_reason);
    }

    /** @test */
    public function a_partial_refund_marks_the_order_partially_refunded()
    {
        $this->fakePaystackRefund(['id' => 900002, 'status' => 'processed']);

        $order   = $this->makeOrder(['total' => 50000]);
        $attempt = $this->service()->request($order, 20000, (string) Str::uuid());

        $this->assertSame('completed', $attempt->status);

        $order->refresh();
        $this->assertSame('partially_refunded', $order->status);
        $this->assertSame(20000.0, (float) $order->refund_amount);
        $this->assertNull($order->refunded_at);
    }

    // ── Idempotency ───────────────────────────────────────────────────────────

    /** @test */
    public function reusing_the_same_request_key_returns_the_same_attempt_without_a_second_gateway_call()
    {
        $this->fakePaystackRefund(['id' => 900003, 'status' => 'processed']);

        $order = $this->makeOrder();
        $key   = (string) Str::uuid();

        $first  = $this->service()->request($order, 50000, $key);
        $second = $this->service()->request($order, 50000, $key);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, RefundAttempt::where('order_id', $order->id)->count());
        Http::assertSentCount(1);
    }

    /** @test */
    public function reusing_a_request_key_with_a_different_amount_is_rejected()
    {
        $this->fakePaystackRefund(['id' => 900004, 'status' => 'processed']);

        $order = $this->makeOrder();
        $key   = (string) Str::uuid();
        $this->service()->request($order, 20000, $key);

        $this->expectException(ValidationException::class);
        $this->service()->request($order, 30000, $key);
    }

    // ── Guards ────────────────────────────────────────────────────────────────

    /** @test */
    public function a_refund_that_exceeds_the_remaining_balance_is_rejected()
    {
        Http::fake();
        $order = $this->makeOrder(['total' => 50000]);

        try {
            $this->service()->request($order, 60000, (string) Str::uuid());
            $this->fail('Expected an over-refund to be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('amount', $e->errors());
        }

        Http::assertNothingSent();
        $this->assertSame(0, RefundAttempt::where('order_id', $order->id)->count());
    }

    /** @test */
    public function a_second_partial_refund_cannot_push_the_total_over_the_order_total()
    {
        $this->fakePaystackRefund(['id' => 900005, 'status' => 'processed']);

        $order = $this->makeOrder(['total' => 50000]);
        $this->service()->request($order, 40000, (string) Str::uuid());

        $this->expectException(ValidationException::class);
        $this->service()->request($order->fresh(), 20000, (string) Str::uuid());
    }

    // ── Failure / uncertainty ───────────────────────────────────────────────────

    /** @test */
    public function a_gateway_rejection_marks_the_attempt_failed_and_never_shows_as_refunded()
    {
        $this->fakePaystackRefund([], 400);

        $order   = $this->makeOrder();
        $attempt = $this->service()->request($order, 50000, (string) Str::uuid());

        $this->assertSame('failed', $attempt->status);
        $this->assertNotNull($attempt->failure_reason);

        $order->refresh();
        $this->assertSame('refund_failed', $order->status);
        $this->assertNull($order->refunded_at);
        $this->assertNotNull($order->refund_failure_reason);
    }

    /** @test */
    public function a_gateway_timeout_parks_the_attempt_as_unknown_for_reconciliation()
    {
        // A 5xx is uncertain, not a failure: the refund may have been accepted. We
        // must never mark it failed (double-refund risk) — park it as 'unknown'.
        $this->fakePaystackRefund([], 500);

        $order   = $this->makeOrder();
        $attempt = $this->service()->request($order, 50000, (string) Str::uuid());

        $this->assertSame('unknown', $attempt->status);
        $this->assertNull($attempt->gateway_id);

        $order->refresh();
        $this->assertSame('refund_pending', $order->status);
        $this->assertNull($order->refunded_at);
    }

    // ── Stripe ──────────────────────────────────────────────────────────────────

    /** @test */
    public function a_stripe_refund_sends_an_idempotency_key_and_the_usd_amount()
    {
        Http::fake(['api.stripe.com/v1/refunds' => Http::response(
            ['id' => 're_abc', 'status' => 'succeeded'], 200
        )]);

        $order = $this->makeOrder(['payment_method' => 'stripe', 'payment_id' => 'pi_live_42', 'total' => 50000, 'total_usd' => 32.50]);
        $key   = (string) Str::uuid();

        $attempt = $this->service()->request($order, 50000, $key);

        $this->assertSame('completed', $attempt->status);
        $this->assertSame('re_abc', $attempt->gateway_id);
        $this->assertSame('refunded', $order->fresh()->status);

        Http::assertSent(function ($request) use ($key) {
            return $request->url() === 'https://api.stripe.com/v1/refunds'
                && $request->hasHeader('Idempotency-Key', $key)
                && (int) $request['amount'] === 3250   // 32.50 USD in cents
                && $request['payment_intent'] === 'pi_live_42';
        });
    }
}
