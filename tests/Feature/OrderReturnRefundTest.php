<?php

namespace Tests\Feature;

use App\Mail\OrderRefunded;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Admin return-review refund flow (AdminOrderController@reviewReturn).
 *
 * Locks the contract that a refund's financial status only ever reflects a
 * verified gateway outcome: a successful-but-queued refund leaves the order
 * refund_pending, and a failed refund is flagged refund_failed (never refunded)
 * with the failure reason persisted for manual reconciliation.
 */
class OrderReturnRefundTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

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
            'status'         => 'delivered',
            'total'          => 50000,
            'total_usd'      => 32.50,
            'payment_method' => 'paystack',
            'reference'      => 'ps_ref_' . uniqid(),
            'payment_id'     => 'pi_' . uniqid(),
            'customer_email' => 'buyer@test.com',
        ], $overrides));
    }

    private function makeReturn(Order $order, array $overrides = []): OrderReturn
    {
        return OrderReturn::create(array_merge([
            'order_id' => $order->id,
            'user_id'  => $order->user_id,
            'reason'   => 'The item arrived faulty and I would like to return it.',
            'status'   => 'pending',
        ], $overrides));
    }

    private function review(OrderReturn $return, string $status)
    {
        return $this->actingAs($this->admin())->patch(
            route('admin.returns.review', $return),
            ['status' => $status, 'admin_notes' => 'Reviewed.']
        );
    }

    /** @test */
    public function a_queued_paystack_refund_leaves_the_order_refund_pending_with_evidence()
    {
        Http::fake([
            'api.paystack.co/refund' => Http::response([
                'status'  => true,
                'message' => 'Refund queued',
                'data'    => ['id' => 424242, 'status' => 'pending'],
            ], 200),
        ]);

        $order  = $this->makeOrder(['total' => 50000]);
        $return = $this->makeReturn($order);

        $this->review($return, 'refunded')->assertRedirect();

        $order->refresh();
        $return->refresh();

        $this->assertSame('refund_pending', $order->status);
        $this->assertSame('424242', (string) $order->refund_id);
        $this->assertSame(50000.0, (float) $order->refund_amount);
        $this->assertNotNull($order->refunded_at);
        $this->assertSame('refunded', $return->status);
        $this->assertSame('424242', (string) $return->refund_id);
        Mail::assertQueued(OrderRefunded::class);
    }

    /** @test */
    public function a_failed_refund_is_flagged_refund_failed_and_never_shown_as_refunded()
    {
        Http::fake([
            'api.paystack.co/refund' => Http::response([
                'status'  => false,
                'message' => 'Transaction has already been fully reversed',
            ], 400),
        ]);

        $order  = $this->makeOrder();
        $return = $this->makeReturn($order);

        $this->review($return, 'refunded')->assertSessionHas('success');

        $order->refresh();
        $return->refresh();

        $this->assertSame('refund_failed', $order->status);
        $this->assertNull($order->refund_id);
        $this->assertNotNull($order->refund_failure_reason);
        // The return falls back to approved for manual handling — no usable refund id.
        $this->assertSame('approved', $return->status);
        $this->assertNull($return->refund_id);
        $this->assertNotNull($return->refund_failure_reason);
        Mail::assertNotQueued(OrderRefunded::class);
    }

    /** @test */
    public function an_unsupported_payment_method_is_flagged_refund_failed()
    {
        Http::fake();

        $order  = $this->makeOrder(['payment_method' => 'cash']);
        $return = $this->makeReturn($order);

        $this->review($return, 'refunded');

        Http::assertNothingSent();

        $this->assertSame('refund_failed', $order->fresh()->status);
        $this->assertNull($return->fresh()->refund_id);
    }

    /** @test */
    public function a_return_with_an_existing_refund_id_is_not_refunded_twice()
    {
        Http::fake();

        $order  = $this->makeOrder();
        $return = $this->makeReturn($order, ['refund_id' => 'existing_ref_999']);

        $this->review($return, 'refunded');

        Http::assertNothingSent();
        $this->assertSame('refunded', $order->fresh()->status);
        $this->assertSame('existing_ref_999', $return->fresh()->refund_id);
    }
}
