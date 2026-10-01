<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Admins can log a return or cancellation on behalf of a customer from the
 * order detail page. It is created as pending so it flows through the existing
 * review/refund logic.
 */
class AdminCreateReturnCancellationTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        return $user;
    }

    private function order(): Order
    {
        $customer = User::factory()->create();
        return Order::create([
            'user_id' => $customer->id, 'status' => 'paid', 'total' => 50000,
            'payment_method' => 'paystack', 'customer_email' => $customer->email,
        ]);
    }

    /** @test */
    public function admin_can_log_a_return_for_an_order()
    {
        $order = $this->order();

        $this->actingAs($this->admin())
            ->post(route('admin.orders.returns.store', $order), [
                'reason' => 'Customer reported the item arrived damaged.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_returns', [
            'order_id' => $order->id,
            'user_id'  => $order->user_id,
            'status'   => 'pending',
        ]);
    }

    /** @test */
    public function admin_can_log_a_cancellation_for_an_order()
    {
        $order = $this->order();

        $this->actingAs($this->admin())
            ->post(route('admin.orders.cancellations.store', $order), [
                'reason' => 'Customer called to cancel before shipping.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_cancellations', [
            'order_id' => $order->id,
            'user_id'  => $order->user_id,
            'status'   => 'pending',
        ]);
    }

    /** @test */
    public function a_reason_is_required()
    {
        $order = $this->order();

        $this->actingAs($this->admin())
            ->post(route('admin.orders.returns.store', $order), ['reason' => 'short'])
            ->assertSessionHasErrors('reason');

        $this->assertDatabaseMissing('order_returns', ['order_id' => $order->id]);
    }

    /** @test */
    public function it_does_not_create_a_duplicate_return()
    {
        $order = $this->order();
        OrderReturn::create([
            'order_id' => $order->id, 'user_id' => $order->user_id,
            'reason' => 'Existing return already on file here.', 'status' => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.orders.returns.store', $order), [
                'reason' => 'Trying to add a second return request.',
            ])
            ->assertRedirect();

        $this->assertSame(1, OrderReturn::where('order_id', $order->id)->count());
    }

    /** @test */
    public function a_non_admin_cannot_log_a_return()
    {
        $order = $this->order();
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->post(route('admin.orders.returns.store', $order), [
                'reason' => 'Not allowed to do this as a normal user.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('order_returns', ['order_id' => $order->id]);
    }
}
