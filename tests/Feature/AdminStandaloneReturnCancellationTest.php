<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Standalone "New Return" / "New Cancellation" flow from the index pages:
 * pick an order via search, then log the request.
 */
class AdminStandaloneReturnCancellationTest extends TestCase
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
    public function create_pages_render()
    {
        $admin = $this->admin();
        $this->actingAs($admin)->get(route('admin.returns.create'))->assertOk()->assertSee('New Return');
        $this->actingAs($admin)->get(route('admin.cancellations.create'))->assertOk()->assertSee('New Cancellation');
    }

    /** @test */
    public function order_search_finds_by_order_number()
    {
        $order = $this->order();

        $res = $this->actingAs($this->admin())
            ->getJson(route('admin.orders.search', ['q' => $order->order_number]));

        $res->assertOk()->assertJsonFragment(['id' => $order->id, 'order_number' => $order->order_number]);
    }

    /** @test */
    public function admin_can_log_a_standalone_return()
    {
        $order = $this->order();

        $this->actingAs($this->admin())->post(route('admin.returns.store'), [
            'order_id' => $order->id,
            'reason'   => 'Customer returned the item at our showroom.',
        ])->assertRedirect(route('admin.returns.index'));

        $this->assertDatabaseHas('order_returns', [
            'order_id' => $order->id, 'status' => 'pending',
        ]);
    }

    /** @test */
    public function admin_can_log_a_standalone_cancellation()
    {
        $order = $this->order();

        $this->actingAs($this->admin())->post(route('admin.cancellations.store'), [
            'order_id' => $order->id,
            'reason'   => 'Customer phoned in to cancel this order.',
        ])->assertRedirect(route('admin.cancellations.index'));

        $this->assertDatabaseHas('order_cancellations', [
            'order_id' => $order->id, 'status' => 'pending',
        ]);
    }

    /** @test */
    public function standalone_return_requires_a_valid_order_and_reason()
    {
        $this->actingAs($this->admin())->post(route('admin.returns.store'), [
            'order_id' => 999999,
            'reason'   => 'too short',
        ])->assertSessionHasErrors(['order_id', 'reason']);
    }

    /** @test */
    public function a_non_admin_cannot_use_the_standalone_form()
    {
        $order = $this->order();
        $this->actingAs(User::factory()->create())
            ->post(route('admin.returns.store'), ['order_id' => $order->id, 'reason' => 'Trying as a normal user here.'])
            ->assertForbidden();
    }
}
