<?php

namespace Tests\Feature;

use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Coverage for the customer-facing self-cancellation flow
 * (OrderController@submitCancel) and the auto-refund it triggers.
 *
 * Paystack refunds go out over HTTP, so Http::fake() covers that path directly.
 * Stripe refunds use the Stripe SDK, so we bind a controller subclass that
 * overrides the createStripeRefund() seam and returns a stubbed Refund — no
 * request ever leaves the machine.
 */
class OrderSelfCancelRefundTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function makeUserAndOrder(array $overrides = []): array
    {
        $user  = User::factory()->create(['email' => 'buyer_' . uniqid() . '@test.com']);
        $order = Order::create(array_merge([
            'user_id'        => $user->id,
            'status'         => 'paid',
            'total'          => 50000,
            'total_usd'      => 32.50,
            'payment_method' => 'paystack',
            'reference'      => 'ps_ref_' . uniqid(),
            'payment_id'     => 'pi_' . uniqid(),
            'customer_email' => 'buyer@test.com',
        ], $overrides));

        return [$user, $order];
    }

    private function submitCancel(User $user, Order $order, string $reason = 'I no longer need this item, please cancel and refund it.')
    {
        return $this->actingAs($user)->post(
            route('account.orders.cancel.submit', $order),
            ['reason' => $reason]
        );
    }

    /**
     * Bind an OrderController whose Stripe refund call is stubbed. Pass a stub
     * Refund object, or null to have the seam throw (simulating an SDK error).
     */
    private function fakeStripeController(?object $stubRefund, bool $throw = false): void
    {
        Http::fake(['api.stripe.com/v1/refunds' => function () use ($stubRefund, $throw) {
            if ($throw) throw new \Illuminate\Http\Client\ConnectionException('Simulated gateway timeout');
            return Http::response($stubRefund ? (array) $stubRefund : ['error' => 'Rejected'], $stubRefund ? 200 : 400);
        }]);
    }

    // ── Paystack ────────────────────────────────────────────────────────────

    /** @test */
    public function paystack_self_cancel_initiates_refund_and_marks_order_refund_pending()
    {
        // Paystack queues the refund (status 'pending'), so the money has not yet
        // moved — the order is refund_pending, not refunded, and carries evidence.
        Http::fake([
            'api.paystack.co/refund' => Http::response([
                'status'  => true,
                'message' => 'Refund queued',
                'data'    => ['id' => 555001, 'status' => 'pending'],
            ], 200),
        ]);

        [$user, $order] = $this->makeUserAndOrder(['payment_method' => 'paystack', 'total' => 50000]);

        $this->submitCancel($user, $order)->assertRedirect();

        $order->refresh();
        $cancellation = $order->cancellation()->first();

        $this->assertSame('refund_pending', $order->status);
        $this->assertSame('555001', (string) $order->refund_id);
        $this->assertSame(50000.0, (float) $order->refund_amount);
        $this->assertNull($order->refunded_at);
        $this->assertNull($order->refund_failure_reason);
        $this->assertNotNull($cancellation);
        $this->assertSame('refunded', $cancellation->status);
        $this->assertSame('555001', (string) $cancellation->refund_id);
        $this->assertNotNull($cancellation->refunded_at);

        Http::assertSent(function ($request) use ($order) {
            return $request->url() === 'https://api.paystack.co/refund'
                && $request['transaction'] === $order->reference
                && (int) $request['amount'] === 5000000
                && $request['currency'] === 'NGN';
        });
    }

    /** @test */
    public function paystack_self_cancel_marks_refund_failed_when_refund_declines()
    {
        Http::fake([
            'api.paystack.co/refund' => Http::response([
                'status'  => false,
                'message' => 'Already reversed',
            ], 400),
        ]);

        [$user, $order] = $this->makeUserAndOrder(['payment_method' => 'paystack']);

        $response = $this->submitCancel($user, $order);
        $response->assertSessionHas('success');

        $order->refresh();
        $cancellation = $order->cancellation()->first();

        // Refund failed: never shown as refunded. The order is flagged
        // refund_failed with the gateway reason, and the request is left
        // 'approved' for the team to refund manually.
        $this->assertSame('refund_failed', $order->status);
        $this->assertNull($order->refund_id);
        $this->assertNotNull($order->refund_failure_reason);
        $this->assertSame('approved', $cancellation->status);
        $this->assertNull($cancellation->refund_id);
        $this->assertStringContainsString('could not process your refund automatically', session('success'));
    }

    // ── Stripe ──────────────────────────────────────────────────────────────

    /** @test */
    public function stripe_self_cancel_auto_refunds_and_marks_order_refunded()
    {
        $this->fakeStripeController((object) ['id' => 're_self_1', 'status' => 'succeeded']);

        [$user, $order] = $this->makeUserAndOrder(['payment_method' => 'stripe', 'payment_id' => 'pi_live_9']);

        $this->submitCancel($user, $order)->assertRedirect();

        $order->refresh();
        $cancellation = $order->cancellation()->first();

        $this->assertSame('refunded', $order->status);
        $this->assertSame('refunded', $cancellation->status);
        $this->assertSame('re_self_1', $cancellation->refund_id);
        $this->assertSame('succeeded', $cancellation->refund_status);
        $this->assertNotNull($cancellation->refunded_at);
    }

    /** @test */
    public function stripe_self_cancel_falls_back_to_approved_when_refund_status_is_not_settled()
    {
        $this->fakeStripeController((object) ['id' => 're_self_2', 'status' => 'failed']);

        [$user, $order] = $this->makeUserAndOrder(['payment_method' => 'stripe', 'payment_id' => 'pi_live_10']);

        $this->submitCancel($user, $order);

        $order->refresh();
        $cancellation = $order->cancellation()->first();

        $this->assertSame('refund_failed', $order->status);
        $this->assertSame('approved', $cancellation->status);
        $this->assertNull($cancellation->refund_id);
    }

    /** @test */
    public function stripe_self_cancel_holds_as_pending_when_gateway_times_out()
    {
        // A timeout is NOT a failure: the refund may have been accepted at Stripe,
        // so we must never mark it failed (that risks a double refund). The attempt
        // is parked as 'unknown' for reconciliation and the order stays refund_pending.
        $this->fakeStripeController(null, throw: true);

        [$user, $order] = $this->makeUserAndOrder(['payment_method' => 'stripe', 'payment_id' => 'pi_live_11']);

        $this->submitCancel($user, $order);

        $order->refresh();
        $cancellation = $order->cancellation()->first();

        $this->assertSame('refund_pending', $order->status);
        $this->assertNull($order->refunded_at);
        // No gateway id was returned, so there is nothing to show as a completed refund.
        $this->assertNull($cancellation->refund_id);
        // The attempt is retained as 'unknown' so it surfaces on the recovery queue.
        $this->assertDatabaseHas('refund_attempts', [
            'order_id' => $order->id,
            'status'   => 'unknown',
        ]);
    }

    /** @test */
    public function stripe_self_cancel_without_a_valid_payment_intent_does_not_refund()
    {
        // payment_id is not a Stripe intent id — the guard rejects it before any
        // SDK call, so no refund is issued.
        [$user, $order] = $this->makeUserAndOrder(['payment_method' => 'stripe', 'payment_id' => 'not-an-intent']);

        $this->submitCancel($user, $order);

        $order->refresh();
        $cancellation = $order->cancellation()->first();

        $this->assertSame('refund_failed', $order->status);
        $this->assertSame('approved', $cancellation->status);
        $this->assertNull($cancellation->refund_id);
    }

    // ── Guards ────────────────────────────────────────────────────────────────

    /** @test */
    public function an_order_cannot_be_cancelled_at_a_non_cancellable_status()
    {
        Http::fake();

        [$user, $order] = $this->makeUserAndOrder(['status' => 'shipped']);

        $response = $this->submitCancel($user, $order);
        $response->assertSessionHas('error');

        Http::assertNothingSent();
        $this->assertSame('shipped', $order->fresh()->status);
        $this->assertNull($order->cancellation()->first());
    }

    /** @test */
    public function a_duplicate_cancellation_request_is_rejected()
    {
        Http::fake();

        [$user, $order] = $this->makeUserAndOrder(['payment_method' => 'paystack']);

        // First request created via the model so the second one hits the guard.
        \App\Models\OrderCancellation::create([
            'order_id' => $order->id,
            'user_id'  => $user->id,
            'reason'   => 'An earlier cancellation request already exists here.',
            'status'   => 'pending',
        ]);

        $response = $this->submitCancel($user, $order);
        $response->assertSessionHas('error');

        // No refund attempted, and still only one cancellation on file.
        Http::assertNothingSent();
        $this->assertSame(1, $order->cancellation()->count());
    }

    /** @test */
    public function cancel_reason_must_meet_the_minimum_length()
    {
        [$user, $order] = $this->makeUserAndOrder();

        $response = $this->submitCancel($user, $order, 'too short');

        $response->assertSessionHasErrors('reason');
        $this->assertNull($order->cancellation()->first());
    }
}
