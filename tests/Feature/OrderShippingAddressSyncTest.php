<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\PendingCheckout;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * The delivery address a customer types at checkout is stored on the order
 * (PendingCheckout) only. The customer's profile address is managed from the
 * account page and is intentionally NEVER overwritten from checkout.
 */
class OrderShippingAddressSyncTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function makeLocation(): Location
    {
        $state = State::create(['name' => 'Rivers ' . uniqid(), 'is_active' => true]);
        return Location::create([
            'name'                => 'Port Harcourt ' . uniqid(),
            'state_id'            => $state->id,
            'is_active'           => true,
            'shipping_cost'       => 3500,
            'truck_shipping_cost' => 20000,
        ]);
    }

    private function makeProduct(): Product
    {
        return Product::create([
            'name'      => 'Standing Fan ' . uniqid(),
            'price'     => 50000,
            'stock'     => 100,
            'is_active' => true,
            'weight_kg' => 5.0,
        ]);
    }

    private function saveDelivery(User $user, Product $product, Location $location, ?string $address): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'customer_email' => 'buyer@test.com',
            'items'          => [['product_id' => $product->id, 'quantity' => 1]],
            'fulfillment'    => [
                'method'               => 'delivery',
                'delivery_location_id' => $location->id,
                'shipping_address'     => $address,
            ],
        ]);
    }

    private function latestCheckout(User $user): ?PendingCheckout
    {
        return PendingCheckout::where('user_id', $user->id)->latest('id')->first();
    }

    /** @test */
    public function a_delivery_address_is_stored_on_the_order_only_not_a_blank_profile()
    {
        $user     = User::factory()->create(['shipping_address' => null]);
        $address  = '12 Aba Road, Off Stadium Junction, Port Harcourt';

        $this->saveDelivery($user, $this->makeProduct(), $this->makeLocation(), $address)->assertOk();

        // The typed address lands on the order…
        $checkout = $this->latestCheckout($user);
        $this->assertNotNull($checkout);
        $this->assertSame($address, $checkout->fulfillment['shipping_address'] ?? null);

        // …but the profile is left blank, never populated from checkout.
        $this->assertNull($user->refresh()->shipping_address);
    }

    /** @test */
    public function a_delivery_order_never_overwrites_an_existing_profile_address()
    {
        $user     = User::factory()->create(['shipping_address' => 'Old address, somewhere']);
        $address  = '7B Ada George Road, Rumuokwuta, Port Harcourt';

        $this->saveDelivery($user, $this->makeProduct(), $this->makeLocation(), $address)->assertOk();

        // The order carries the freshly typed address…
        $checkout = $this->latestCheckout($user);
        $this->assertSame($address, $checkout->fulfillment['shipping_address'] ?? null);

        // …while the profile address stays exactly as it was.
        $this->assertSame('Old address, somewhere', $user->refresh()->shipping_address);
    }

    /** @test */
    public function a_pickup_order_does_not_touch_the_profile_address()
    {
        $user    = User::factory()->create(['shipping_address' => 'Keep this untouched']);
        $product = $this->makeProduct();

        $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'customer_email' => 'buyer@test.com',
            'items'          => [['product_id' => $product->id, 'quantity' => 1]],
            'fulfillment'    => ['method' => 'pickup', 'pickup_location' => 'Enugu HQ'],
        ])->assertOk();

        $this->assertSame('Keep this untouched', $user->refresh()->shipping_address);
    }
}
