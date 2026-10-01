<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\PendingCheckout;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Coverage for the truck-delivery decision in OrderController::saveCheckout().
 *
 * A cart must be charged the location's `truck_shipping_cost` (instead of the
 * regular `shipping_cost`) when ANY of these is true:
 *   1. A product / subcategory / category carries the requires_truck flag.
 *   2. Total cart weight >  truck_weight_threshold_kg   (strictly over — the exact
 *      boundary stays on regular shipping, matching the browser and admin copy).
 *   3. Order value      >= truck_order_value_threshold_ngn.
 *
 * The server is the source of truth (the browser only sends IDs + quantities),
 * so every trigger has to be re-decided here. These tests lock that contract.
 *
 * Uses DatabaseTransactions (like InstallationOptionsTest) so writes roll back
 * against the shared test database.
 */
class CheckoutTruckDeliveryTest extends TestCase
{
    use DatabaseTransactions;

    private const REGULAR_FEE = 2000;
    private const TRUCK_FEE   = 15000;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->withoutMiddleware(ThrottleRequests::class);

        // Deterministic thresholds regardless of what the DB happens to hold.
        // (Setting keeps a static cache, so reset it before overriding.)
        Setting::clearCache();
        Setting::set('truck_weight_threshold_kg', 30);
        Setting::set('truck_order_value_threshold_ngn', 1000000);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** A delivery location with distinct regular vs truck rates. */
    private function makeLocation(): Location
    {
        return Location::create([
            'name'                => 'Test City ' . uniqid(),
            'is_active'           => true,
            'shipping_cost'       => self::REGULAR_FEE,
            'truck_shipping_cost' => self::TRUCK_FEE,
        ]);
    }

    /**
     * Create a product. Markup/discount are left null so sell_price == price,
     * which keeps the order-value math easy to reason about.
     */
    private function makeProduct(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name'      => 'Test Product ' . uniqid(),
            'price'     => 10000,   // well under the ₦1,000,000 value threshold
            'stock'     => 100,
            'is_active' => true,
            'weight_kg' => 5.0,     // well under the 30 kg weight threshold
        ], $attrs));
    }

    /** POST a single-item delivery checkout and return the decoded fee/total. */
    private function checkoutDelivery(User $user, Product $product, int $qty, Location $location): array
    {
        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items'       => [[
                'product_id' => $product->id,
                'quantity'   => $qty,
            ]],
            'fulfillment' => [
                'method'               => 'delivery',
                'delivery_location_id' => $location->id,
            ],
        ]);

        $res->assertOk();
        $pending = PendingCheckout::where('reference', $res->json('reference'))->firstOrFail();

        return [
            'fee'   => (int) ($pending->fulfillment['delivery_fee_ngn'] ?? -1),
            'total' => (int) $res->json('total_ngn'),
        ];
    }

    // ── Control: no trigger → regular shipping ──────────────────────────────

    /** @test */
    public function delivery_uses_regular_shipping_when_nothing_triggers_a_truck()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct();               // 5 kg, ₦10,000, no flag
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::REGULAR_FEE, $out['fee']);
        $this->assertSame(10000 + self::REGULAR_FEE, $out['total']);
    }

    // ── Trigger 2: weight threshold ─────────────────────────────────────────

    /** @test */
    public function weight_over_threshold_triggers_the_truck_fee()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct(['weight_kg' => 40]);   // 40 kg > 30 kg
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::TRUCK_FEE, $out['fee']);
        $this->assertSame(10000 + self::TRUCK_FEE, $out['total']);
    }

    /** @test */
    public function weight_accumulates_across_quantity_to_cross_the_threshold()
    {
        // 16 kg each × 2 = 32 kg > 30, even though a single unit is under.
        $user     = User::factory()->create();
        $product  = $this->makeProduct(['weight_kg' => 16]);
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 2, $location);

        $this->assertSame(self::TRUCK_FEE, $out['fee']);
    }

    /** @test */
    public function weight_exactly_at_threshold_stays_on_regular_shipping()
    {
        // Boundary: the server must use strictly greater-than, exactly like the
        // browser (checkout.blade.php) and the admin copy ("exceeds / above").
        // A cart sitting precisely on 30 kg must NOT be surprised with a truck fee.
        $user     = User::factory()->create();
        $product  = $this->makeProduct(['weight_kg' => 30]);   // 30 kg == 30 kg
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::REGULAR_FEE, $out['fee']);
    }

    /** @test */
    public function weight_just_below_threshold_stays_on_regular_shipping()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct(['weight_kg' => 29]);   // 29 kg < 30 kg
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::REGULAR_FEE, $out['fee']);
    }

    /** @test */
    public function weight_trickles_down_from_category_when_product_has_none()
    {
        // Product carries no weight_kg; the 45 kg category estimate must apply.
        $user     = User::factory()->create();
        $category = Category::create(['name' => 'Heavy Cat ' . uniqid(), 'estimated_weight_kg' => 45]);
        $product  = $this->makeProduct(['weight_kg' => null, 'category_id' => $category->id]);
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::TRUCK_FEE, $out['fee']);
    }

    // ── Trigger 3: order-value threshold ────────────────────────────────────

    /** @test */
    public function order_value_at_or_above_threshold_triggers_the_truck_fee()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct(['price' => 1000000, 'weight_kg' => 1]); // ₦1,000,000, light
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::TRUCK_FEE, $out['fee']);
        $this->assertSame(1000000 + self::TRUCK_FEE, $out['total']);
    }

    /** @test */
    public function order_value_just_below_threshold_stays_on_regular_shipping()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct(['price' => 999999, 'weight_kg' => 1]);
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::REGULAR_FEE, $out['fee']);
    }

    // ── Trigger 1: the requires_truck flag and its cascade ──────────────────

    /** @test */
    public function product_requires_truck_flag_triggers_the_truck_fee()
    {
        $user     = User::factory()->create();
        $product  = $this->makeProduct(['requires_truck' => true]); // light + cheap, flag only
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::TRUCK_FEE, $out['fee']);
    }

    /** @test */
    public function category_requires_truck_cascades_to_the_truck_fee()
    {
        // Product inherits (requires_truck = null); the category flag decides.
        $user     = User::factory()->create();
        $category = Category::create(['name' => 'Truck Cat ' . uniqid(), 'requires_truck' => true]);
        $product  = $this->makeProduct(['requires_truck' => null, 'category_id' => $category->id]);
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::TRUCK_FEE, $out['fee']);
    }

    /** @test */
    public function subcategory_requires_truck_cascades_to_the_truck_fee()
    {
        $user        = User::factory()->create();
        $category    = Category::create(['name' => 'Plain Cat ' . uniqid(), 'requires_truck' => false]);
        $subcategory = Subcategory::create([
            'name'           => 'Truck Sub ' . uniqid(),
            'category_id'    => $category->id,
            'requires_truck' => true,
        ]);
        $product = $this->makeProduct([
            'requires_truck' => null,
            'category_id'    => $category->id,
            'subcategory_id' => $subcategory->id,
        ]);
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::TRUCK_FEE, $out['fee']);
    }

    /** @test */
    public function explicit_product_false_overrides_a_truck_category()
    {
        // requires_truck = false means "never truck", even under a truck category,
        // as long as weight/value stay under their thresholds.
        $user     = User::factory()->create();
        $category = Category::create(['name' => 'Truck Cat ' . uniqid(), 'requires_truck' => true]);
        $product  = $this->makeProduct(['requires_truck' => false, 'category_id' => $category->id]);
        $location = $this->makeLocation();

        $out = $this->checkoutDelivery($user, $product, 1, $location);

        $this->assertSame(self::REGULAR_FEE, $out['fee']);
    }

    // ── Pickup is never charged a delivery fee ──────────────────────────────

    /** @test */
    public function pickup_is_never_charged_a_truck_fee_even_when_triggered()
    {
        $user    = User::factory()->create();
        $product = $this->makeProduct(['weight_kg' => 100, 'requires_truck' => true]);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items'       => [['product_id' => $product->id, 'quantity' => 1]],
            'fulfillment' => ['method' => 'pickup'],
        ]);

        $res->assertOk();
        $pending = PendingCheckout::where('reference', $res->json('reference'))->firstOrFail();

        $this->assertSame(0, (int) ($pending->fulfillment['delivery_fee_ngn'] ?? -1));
        $this->assertSame(10000, (int) $res->json('total_ngn'));   // no delivery fee added
    }
}
