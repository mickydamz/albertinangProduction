<?php

namespace Tests\Feature;

use App\Mail\OrderCancelled;
use App\Mail\OrderCancellationRejected;
use App\Mail\OrderRefunded;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Covers the admin cancellation-review flow and the gateway refund it triggers
 * (AdminOrderController@reviewCancellation + refundOrder helpers) for both
 * Paystack and Stripe.
 *
 * Both gateways issue refunds over HTTP, so Http::fake() lets us assert the
 * exact request that goes out and the resulting order / cancellation state.
 */
class OrderCancellationRefundTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();

        return $user;
    }

    private function makeOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'user_id'        => User::factory()->create(['email' => 'buyer_' . uniqid() . '@test.com'])->id,
            'status'         => 'cancelled',
            'total'          => 50000,          // ₦50,000
            'total_usd'      => 32.50,          // $32.50
            'payment_method' => 'paystack',
            'reference'      => 'ps_ref_' . uniqid(),
            'payment_id'     => 'pi_' . uniqid(),
            'customer_email' => 'buyer@test.com',
        ], $overrides));
    }

    private function makeCancellation(Order $order, array $overrides = []): OrderCancellation
    {
        return OrderCancellation::create(array_merge([
            'order_id' => $order->id,
            'user_id'  => $order->user_id,
            'reason'   => 'I changed my mind about this purchase entirely, please cancel it.',
            'status'   => 'pending',
        ], $overrides));
    }

    private function review(OrderCancellation $cancellation, string $status, ?string $notes = 'Reviewed by admin.')
    {
        return $this->actingAs($this->admin())->patch(
            route('admin.cancellations.review', $cancellation),
            ['status' => $status, 'admin_notes' => $notes]
        );
    }

    // ── Paystack ────────────────────────────────────────────────────────────

    /** @test */
    public function paystack_refund_succeeds_and_marks_order_and_cancellation_refunded()
    {
        Http::fake([
            'api.paystack.co/refund' => Http::response([
                'status'  => true,
                'message' => 'Refund has been queued for processing',
                'data'    => ['id' => 987654, 'status' => 'pending'],
            ], 200),
        ]);

        $order        = $this->makeOrder(['payment_method' => 'paystack', 'total' => 50000]);
        $cancellation = $this->makeCancellation($order);

        $this->review($cancellation, 'refunded')->assertRedirect();

        $order->refresh();
        $cancellation->refresh();

        $this->assertSame('refunded', $order->status);
        $this->assertSame('refunded', $cancellation->status);
        $this->assertSame('987654', (string) $cancellation->refund_id);
        $this->assertSame('pending', $cancellation->refund_status);
        $this->assertNotNull($cancellation->refunded_at);

        // The right request was sent: correct endpoint, transaction ref, and
        // amount converted from naira to kobo.
        Http::assertSent(function ($request) use ($order) {
            return $request->url() === 'https://api.paystack.co/refund'
                && $request['transaction'] === $order->reference
                && (int) $request['amount'] === 5000000   // ₦50,000 -> kobo
                && $request['currency'] === 'NGN';
        });

        Mail::assertQueued(OrderRefunded::class);
    }

    /** @test */
    public function paystack_refund_failure_marks_refunded_but_flags_for_manual_handling()
    {
        Http::fake([
            'api.paystack.co/refund' => Http::response([
                'status'  => false,
                'message' => 'Transaction has already been fully reversed',
            ], 400),
        ]);

        $order        = $this->makeOrder(['payment_method' => 'paystack']);
        $cancellation = $this->makeCancellation($order);

        $response = $this->review($cancellation, 'refunded');
        $response->assertSessionHas('success');

        $order->refresh();
        $cancellation->refresh();

        // Order is still moved to refunded, but no gateway refund id was stored
        // and the admin is told to handle it manually.
        $this->assertSame('refunded', $order->status);
        $this->assertNull($cancellation->refund_id);
        $this->assertStringContainsString('process manually', session('success'));
    }

    /** @test */
    public function paystack_refund_is_not_reissued_when_cancellation_already_has_a_refund_id()
    {
        Http::fake();

        $order        = $this->makeOrder(['payment_method' => 'paystack']);
        $cancellation = $this->makeCancellation($order, ['refund_id' => 'existing_ref_123']);

        $this->review($cancellation, 'refunded');

        // No second refund call — protects against double refunds.
        Http::assertNothingSent();

        $order->refresh();
        $this->assertSame('refunded', $order->status);
        $this->assertSame('existing_ref_123', $cancellation->fresh()->refund_id);
    }

    // ── Stripe ──────────────────────────────────────────────────────────────

    /** @test */
    public function stripe_refund_succeeds_and_marks_order_and_cancellation_refunded()
    {
        Http::fake([
            'api.stripe.com/v1/refunds' => Http::response([
                'id'     => 're_test_123',
                'object' => 'refund',
                'status' => 'succeeded',
            ], 200),
        ]);

        $order        = $this->makeOrder(['payment_method' => 'stripe', 'payment_id' => 'pi_abc123', 'total_usd' => 32.50]);
        $cancellation = $this->makeCancellation($order);

        $this->review($cancellation, 'refunded')->assertRedirect();

        $order->refresh();
        $cancellation->refresh();

        $this->assertSame('refunded', $order->status);
        $this->assertSame('refunded', $cancellation->status);
        $this->assertSame('re_test_123', $cancellation->refund_id);
        $this->assertSame('succeeded', $cancellation->refund_status);
        $this->assertNotNull($cancellation->refunded_at);

        // Correct endpoint, payment intent, and USD amount in cents.
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.stripe.com/v1/refunds')
                && $request['payment_intent'] === 'pi_abc123'
                && (int) $request['amount'] === 3250;   // $32.50 -> cents
        });

        Mail::assertQueued(OrderRefunded::class);
    }

    /** @test */
    public function stripe_refund_without_a_payment_intent_flags_for_manual_handling()
    {
        Http::fake();

        $order        = $this->makeOrder(['payment_method' => 'stripe', 'payment_id' => null, 'reference' => null]);
        $cancellation = $this->makeCancellation($order);

        $response = $this->review($cancellation, 'refunded');

        Http::assertNothingSent();

        $order->refresh();
        $cancellation->refresh();

        $this->assertSame('refunded', $order->status);
        $this->assertNull($cancellation->refund_id);
        $this->assertStringContainsString('process manually', session('success'));
    }

    // ── Unsupported gateway ───────────────────────────────────────────────────

    /** @test */
    public function unsupported_payment_method_cannot_be_auto_refunded()
    {
        Http::fake();

        $order        = $this->makeOrder(['payment_method' => 'cash']);
        $cancellation = $this->makeCancellation($order);

        $response = $this->review($cancellation, 'refunded');

        Http::assertNothingSent();

        $order->refresh();
        $this->assertSame('refunded', $order->status);
        $this->assertNull($cancellation->fresh()->refund_id);
        $this->assertStringContainsString('auto-refunded', session('success'));
    }

    // ── Approve / reject (no refund) ──────────────────────────────────────────

    /** @test */
    public function approving_a_cancellation_marks_the_order_cancelled_without_refunding()
    {
        Http::fake();

        $order        = $this->makeOrder(['payment_method' => 'paystack', 'status' => 'paid']);
        $cancellation = $this->makeCancellation($order);

        $this->review($cancellation, 'approved');

        Http::assertNothingSent();

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        $this->assertSame('approved', $cancellation->fresh()->status);

        Mail::assertQueued(OrderCancelled::class);
        Mail::assertNotQueued(OrderRefunded::class);
    }

    /** @test */
    public function rejecting_a_cancellation_restores_a_cancelled_order_to_paid()
    {
        Http::fake();

        $order        = $this->makeOrder(['payment_method' => 'paystack', 'status' => 'cancelled']);
        $cancellation = $this->makeCancellation($order);

        $this->review($cancellation, 'rejected');

        Http::assertNothingSent();

        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertSame('rejected', $cancellation->fresh()->status);

        Mail::assertQueued(OrderCancellationRejected::class);
    }

    // ── Validation & authorization ────────────────────────────────────────────

    /** @test */
    public function review_rejects_an_invalid_status()
    {
        $order        = $this->makeOrder();
        $cancellation = $this->makeCancellation($order);

        $response = $this->actingAs($this->admin())->patch(
            route('admin.cancellations.review', $cancellation),
            ['status' => 'not-a-real-status']
        );

        $response->assertSessionHasErrors('status');
        $this->assertSame('pending', $cancellation->fresh()->status);
    }

    /** @test */
    public function a_non_admin_cannot_review_a_cancellation()
    {
        Http::fake();

        $order        = $this->makeOrder(['payment_method' => 'paystack', 'status' => 'paid']);
        $cancellation = $this->makeCancellation($order);

        $customer = User::factory()->create(); // default role, not admin

        $response = $this->actingAs($customer)->patch(
            route('admin.cancellations.review', $cancellation),
            ['status' => 'approved']
        );

        $response->assertForbidden();
        Http::assertNothingSent();
        $this->assertSame('paid', $order->fresh()->status);
    }
}
