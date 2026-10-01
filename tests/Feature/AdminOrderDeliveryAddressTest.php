<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Admin-created delivery orders capture a shipping address.
 */
class AdminOrderDeliveryAddressTest extends TestCase
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

    /** @test */
    public function admin_delivery_order_saves_the_shipping_address()
    {
        $state    = State::create(['name' => 'Rivers ' . uniqid(), 'is_active' => true]);
        $location = Location::create([
            'name' => 'PH ' . uniqid(), 'state_id' => $state->id, 'is_active' => true,
            'shipping_cost' => 3500, 'truck_shipping_cost' => 20000,
        ]);
        $product = Product::create(['name' => 'Fan', 'price' => 20000, 'stock' => 5, 'is_active' => true]);

        $address = '7B Ada George Road, Rumuokwuta, Port Harcourt';

        $this->actingAs($this->admin())->post(route('admin.orders.store'), [
            'customer_email'       => 'buyer@test.com',
            'payment_method'       => 'cash',
            'fulfillment_method'   => 'delivery',
            'delivery_state_id'    => $state->id,
            'delivery_location_id' => $location->id,
            'shipping_address'     => $address,
            'shipping_cost'        => 3500,
            'items'                => [[
                'product_id' => $product->id, 'quantity' => 1, 'price' => 20000,
            ]],
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_email'      => 'buyer@test.com',
            'fulfillment_method'  => 'delivery',
            'delivery_state_id'   => $state->id,
            'shipping_address'    => $address,
            'total'               => 23500, // 20,000 + 3,500 shipping
        ]);
    }

    /** @test */
    public function pickup_order_does_not_store_a_shipping_address()
    {
        $product = Product::create(['name' => 'Kettle', 'price' => 8000, 'stock' => 5, 'is_active' => true]);
        $state    = State::create(['name' => 'Lagos ' . uniqid(), 'is_active' => true]);
        $location = Location::create([
            'name' => 'Ikeja ' . uniqid(), 'state_id' => $state->id, 'is_active' => true,
            'shipping_cost' => 2000, 'truck_shipping_cost' => 15000,
        ]);
        $pickup = \App\Models\PickupPoint::create([
            'name' => 'HQ ' . uniqid(), 'location_id' => $location->id, 'address' => '1 Rd',
        ]);

        $this->actingAs($this->admin())->post(route('admin.orders.store'), [
            'customer_email'     => 'buyer3@test.com',
            'payment_method'     => 'cash',
            'fulfillment_method' => 'pickup',
            'pickup_point_id'    => $pickup->id,
            // shipping_address deliberately sent — must be ignored for pickup
            'shipping_address'   => 'Should be ignored',
            'items'              => [['product_id' => $product->id, 'quantity' => 1, 'price' => 8000]],
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_email'     => 'buyer3@test.com',
            'fulfillment_method' => 'pickup',
            'shipping_address'   => null,
        ]);
    }
}
