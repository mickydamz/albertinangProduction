<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Customer self-serve return eligibility (OrderController@submitReturn +
 * Order::isEligibleForReturn). A return is only accepted for a delivered/shipped/
 * completed order within the 14-day window, and only once per order.
 */
class OrderReturnWindowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function makeUserAndOrder(array $overrides = []): array
    {
        $user  = User::factory()->create(['email' => 'buyer_' . uniqid() . '@test.com']);
        $order = Order::create(array_merge([
            'user_id'        => $user->id,
            'status'         => 'delivered',
            'total'          => 50000,
            'payment_method' => 'paystack',
            'customer_email' => 'buyer@test.com',
            'delivered_at'   => now(),
        ], $overrides));

        return [$user, $order];
    }

    private function submitReturn(User $user, Order $order, string $reason = 'The item arrived damaged and I would like to return it please.')
    {
        return $this->actingAs($user)->post(route('account.orders.return.submit', $order), ['reason' => $reason]);
    }

    /** @test */
    public function a_delivered_order_within_the_window_can_be_returned()
    {
        [$user, $order] = $this->makeUserAndOrder(['delivered_at' => now()->subDays(3)]);

        $this->submitReturn($user, $order)->assertRedirect(route('account.orders'));

        $this->assertSame(1, OrderReturn::where('order_id', $order->id)->count());
    }

    /** @test */
    public function a_return_requested_after_the_window_is_rejected()
    {
        [$user, $order] = $this->makeUserAndOrder(['delivered_at' => now()->subDays(20)]);

        $this->submitReturn($user, $order)->assertSessionHas('error');

        $this->assertSame(0, OrderReturn::where('order_id', $order->id)->count());
    }

    /** @test */
    public function a_return_exactly_at_the_window_boundary_is_still_accepted()
    {
        // isEligibleForReturn uses >= now()->subDays(14); give a little slack so the
        // test clock doesn't tip just past the boundary between the two now() calls.
        [$user, $order] = $this->makeUserAndOrder(['delivered_at' => now()->subDays(14)->addMinutes(1)]);

        $this->submitReturn($user, $order)->assertRedirect(route('account.orders'));

        $this->assertSame(1, OrderReturn::where('order_id', $order->id)->count());
    }

    /** @test */
    public function an_order_that_has_not_shipped_cannot_be_returned()
    {
        [$user, $order] = $this->makeUserAndOrder(['status' => 'paid', 'delivered_at' => null]);

        $this->submitReturn($user, $order)->assertSessionHas('error');

        $this->assertSame(0, OrderReturn::where('order_id', $order->id)->count());
    }

    /** @test */
    public function an_order_cannot_be_returned_twice()
    {
        [$user, $order] = $this->makeUserAndOrder(['delivered_at' => now()->subDays(2)]);
        OrderReturn::create([
            'order_id' => $order->id,
            'user_id'  => $user->id,
            'reason'   => 'An earlier return request already exists for this order here.',
            'status'   => 'pending',
        ]);

        $this->submitReturn($user, $order)->assertSessionHas('error');

        $this->assertSame(1, OrderReturn::where('order_id', $order->id)->count());
    }

    /** @test */
    public function a_customer_cannot_open_a_return_for_another_customers_order()
    {
        [, $order]   = $this->makeUserAndOrder(['delivered_at' => now()->subDays(2)]);
        $otherPerson = User::factory()->create();

        $this->submitReturn($otherPerson, $order)->assertForbidden();

        $this->assertSame(0, OrderReturn::where('order_id', $order->id)->count());
    }
}
