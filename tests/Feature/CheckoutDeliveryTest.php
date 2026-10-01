<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use App\Services\PaystackOrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Home-delivery checkout: a customer picks "delivery", chooses a location, and
 * types a shipping address. Everything the server persists must be derived from
 * the database (fees, names) except the free-text address, which is the one thing
 * the customer actually types — it has to survive intact all the way onto the order.
 *
 * Pipeline under test:
 *   saveCheckout()  → trusted PendingCheckout snapshot (address + resolved fee)
 *   PaystackOrderService::fulfil() → snapshot → Order (delivery fields + address)
 *
 * Uses DatabaseTransactions (like InstallationOptionsTest) so every write rolls back.
 */
class CheckoutDeliveryTest extends TestCase
{
    use DatabaseTransactions;

    private PaystackOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaystackOrderService::class);
        Mail::fake();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function makeLocation(float $shipping = 3500, float $truck = 20000): Location
    {
        $state = State::create(['name' => 'Rivers ' . uniqid(), 'is_active' => true]);
        return Location::create([
            'name'                => 'Port Harcourt ' . uniqid(),
            'state_id'            => $state->id,
            'is_active'           => true,
            'shipping_cost'       => $shipping,
            'truck_shipping_cost' => $truck,
        ]);
    }

    private function makeProduct(float $price = 50000, array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name'      => 'Standing Fan ' . uniqid(),
            'price'     => $price,
            'stock'     => 100,
            'is_active' => true,
            'weight_kg' => 5.0,
        ], $attrs));
    }

    private function mockPaystackOk(string $reference, int $amountKobo): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data'   => [
                    'id'        => 424242,
                    'reference' => $reference,
                    'status'    => 'success',
                    'amount'    => $amountKobo,
                    'currency'  => 'NGN',
                    'customer'  => ['email' => 'buyer@test.com'],
                ],
            ], 200),
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

    // ── saveCheckout: the trusted snapshot ──────────────────────────────────

    /** @test */
    public function save_checkout_stores_the_customer_address_and_resolved_delivery_fields()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct(50000);
        $location = $this->makeLocation(3500, 20000);
        $address  = '12 Aba Road, Off Stadium Junction, Port Harcourt';

        $res = $this->saveDelivery($user, $product, $location, $address);
        $res->assertOk();

        $pending = PendingCheckout::where('reference', $res->json('reference'))->firstOrFail();
        $f = $pending->fulfillment;

        $this->assertSame('delivery', $f['method']);
        $this->assertSame($address, $f['shipping_address']);            // the typed address survives
        $this->assertSame($location->id, $f['delivery_location_id']);
        $this->assertSame($location->name, $f['delivery_location_name']); // name resolved from DB
        $this->assertSame($location->state->name, $f['delivery_state_name']);
        $this->assertSame(3500, (int) $f['delivery_fee_ngn']);           // regular fee (light product)
        $this->assertSame(53500, (int) $res->json('total_ngn'));         // 50000 + 3500
    }

    /** @test */
    public function save_checkout_ignores_any_client_supplied_fee_and_uses_the_database_fee()
    {
        // The browser only sends IDs + the address. Even if a hostile client tries
        // to inject a delivery fee, the server must use the location's DB value.
        $user     = User::factory()->create();
        $product  = $this->makeProduct(50000);
        $location = $this->makeLocation(3500, 20000);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items'       => [['product_id' => $product->id, 'quantity' => 1]],
            'fulfillment' => [
                'method'               => 'delivery',
                'delivery_location_id' => $location->id,
                'shipping_address'     => '5 Trans-Amadi',
                'delivery_fee_ngn'     => 1,   // hostile — must be ignored
            ],
        ]);

        $res->assertOk();
        $pending = PendingCheckout::where('reference', $res->json('reference'))->firstOrFail();
        $this->assertSame(3500, (int) $pending->fulfillment['delivery_fee_ngn']);
        $this->assertSame(53500, (int) $res->json('total_ngn'));
    }

    /** @test */
    public function save_checkout_rejects_an_inactive_delivery_location()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct(50000);
        $location = $this->makeLocation();
        $location->update(['is_active' => false]);

        $res = $this->saveDelivery($user, $product, $location, '1 Somewhere St');
        $res->assertStatus(422);
    }

    // ── End-to-end: address reaches the paid order ──────────────────────────

    /** @test */
    public function delivery_address_and_fields_survive_from_checkout_to_the_paid_order()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct(50000);
        $location = $this->makeLocation(3500, 20000);
        $address  = '7B Ada George Road, Rumuokwuta, Port Harcourt';

        // 1. Customer saves the delivery checkout with a typed address.
        $save = $this->saveDelivery($user, $product, $location, $address);
        $save->assertOk();
        $ref      = $save->json('reference');
        $totalNgn = $save->json('total_ngn');
        $this->assertEquals(53500, $totalNgn);

        // 2. Paystack verifies payment for exactly that amount.
        $this->mockPaystackOk($ref, (int) round($totalNgn * 100));

        // 3. Fulfilment builds the order from the trusted snapshot.
        $result = $this->service->fulfil($ref, $user->id);
        $this->assertTrue($result['success'], $result['message'] ?? '');

        // 4. The order carries the full delivery record — address included.
        $this->assertDatabaseHas('orders', [
            'reference'              => $ref,
            'fulfillment_method'     => 'delivery',
            'delivery_location_id'   => $location->id,
            'delivery_location_name' => $location->name,
            'delivery_state_name'    => $location->state->name,
            'shipping_address'       => $address,
            'shipping_cost'          => 3500,
        ]);
        $order = Order::where('reference', $ref)->first();
        $this->assertEquals(53500, (float) $order->total);
    }

    /** @test */
    public function heavy_delivery_uses_the_truck_fee_and_still_records_the_address()
    {
        // A truck-requiring product must be charged the truck rate, and the typed
        // address must still land on the order.
        $user     = User::factory()->create();
        $product  = $this->makeProduct(50000, ['requires_truck' => true]);
        $location = $this->makeLocation(3500, 20000);
        $address  = '3 Refinery Road, Eleme';

        $save = $this->saveDelivery($user, $product, $location, $address);
        $save->assertOk();
        $ref = $save->json('reference');
        $this->assertEquals(70000, $save->json('total_ngn')); // 50000 + 20000 truck fee

        $this->mockPaystackOk($ref, 7000000);
        $result = $this->service->fulfil($ref, $user->id);
        $this->assertTrue($result['success'], $result['message'] ?? '');

        $this->assertDatabaseHas('orders', [
            'reference'        => $ref,
            'shipping_address' => $address,
            'shipping_cost'    => 20000,
        ]);
    }
}
