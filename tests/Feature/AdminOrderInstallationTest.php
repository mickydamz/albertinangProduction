<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\PickupPoint;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Admin-created orders can carry installation options per item, just like the
 * customer checkout flow.
 */
class AdminOrderInstallationTest extends TestCase
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

    private function pickupPoint(): PickupPoint
    {
        $state    = State::create(['name' => 'Rivers ' . uniqid(), 'is_active' => true]);
        $location = Location::create([
            'name' => 'PH ' . uniqid(), 'state_id' => $state->id, 'is_active' => true,
            'shipping_cost' => 3500, 'truck_shipping_cost' => 20000,
        ]);
        return PickupPoint::create(['name' => 'HQ ' . uniqid(), 'location_id' => $location->id, 'address' => '1 Rd']);
    }

    /** @test */
    public function admin_order_stores_installation_option_and_extra_on_the_item()
    {
        $product = Product::create([
            'name' => 'Hisense 65" TV', 'price' => 50000, 'stock' => 10, 'is_active' => true,
            'installation_options' => [['label' => 'Add TV Wall Mount', 'price' => 15000]],
        ]);
        $pickup = $this->pickupPoint();

        $response = $this->actingAs($this->admin())->post(route('admin.orders.store'), [
            'customer_email'     => 'buyer@test.com',
            'payment_method'     => 'cash',
            'fulfillment_method' => 'pickup',
            'pickup_point_id'    => $pickup->id,
            'items'              => [[
                'product_id'             => $product->id,
                'quantity'               => 1,
                'price'                  => 65000,   // effective: 50,000 base + 15,000 install
                'installation_option'    => 'Add TV Wall Mount',
                'installation_extra_ngn' => 15000,
            ]],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('order_items', [
            'product_id'             => $product->id,
            'price'                  => 65000,
            'installation_option'    => 'Add TV Wall Mount',
            'installation_extra_ngn' => 15000,
        ]);

        // Order total reflects the effective (installation-inclusive) price.
        $this->assertDatabaseHas('orders', [
            'customer_email' => 'buyer@test.com',
            'total'          => 65000,
        ]);
    }

    /** @test */
    public function admin_order_without_installation_still_works()
    {
        $product = Product::create([
            'name' => 'Kettle', 'price' => 8000, 'stock' => 10, 'is_active' => true,
        ]);
        $pickup = $this->pickupPoint();

        $this->actingAs($this->admin())->post(route('admin.orders.store'), [
            'customer_email'     => 'buyer2@test.com',
            'payment_method'     => 'cash',
            'fulfillment_method' => 'pickup',
            'pickup_point_id'    => $pickup->id,
            'items'              => [[
                'product_id' => $product->id,
                'quantity'   => 2,
                'price'      => 8000,
            ]],
        ])->assertRedirect();

        $this->assertDatabaseHas('order_items', [
            'product_id'             => $product->id,
            'price'                  => 8000,
            'quantity'               => 2,
            'installation_extra_ngn' => 0,
        ]);
    }
}
