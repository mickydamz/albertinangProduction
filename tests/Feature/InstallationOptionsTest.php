<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\Product;
use App\Models\User;
use App\Services\PaystackOrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * End-to-end coverage for the installation-options feature across BOTH payment
 * gateways (Paystack + Stripe) and every layer of the pipeline:
 *
 *   1. saveCheckout()          — builds the trusted PendingCheckout snapshot
 *   2. PaystackOrderService    — snapshot  → order_items   (Paystack path)
 *   3. storeStripeOrder()      — payload   → order_items   (Stripe path)
 *   4. InstallationOptions API — options fed to the checkout UI
 *
 * These tests exist to lock down the exact regression that shipped once before:
 * a customer picks an installation option, pays, and the order arrives with the
 * option dropped. Every path that can carry (or lose) installation data has a
 * test here. Do not delete these when touching checkout/paystack/order code.
 *
 * Uses DatabaseTransactions (NOT RefreshDatabase) so the suite is safe to run
 * against the shared MySQL database — every write is rolled back.
 */
class InstallationOptionsTest extends TestCase
{
    use DatabaseTransactions;

    private PaystackOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaystackOrderService::class);
        Mail::fake();
        // Throttle middleware would otherwise flake across the many POSTs below.
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function makeProduct(array $options = [], float $price = 50000, bool $active = true): Product
    {
        return Product::create([
            'name'                 => 'Test Product ' . uniqid(),
            'price'                => $price,
            'stock'                => 100,
            'is_active'            => $active,
            'installation_options' => $options,
        ]);
    }

    private function tvOption(): array
    {
        return ['label' => 'Add TV Installation Service', 'price' => 14500, 'description' => ''];
    }

    private function premiumOptions(): array
    {
        return [
            ['label' => 'Standard', 'price' => 0,    'description' => 'Included'],
            ['label' => 'Premium',  'price' => 8000, 'description' => 'Wall mount + calibration'],
        ];
    }

    private function mockPaystack(string $reference, int $amountKobo, string $currency = 'NGN'): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status'  => true,
                'message' => 'Verification successful',
                'data'    => [
                    'id'        => 987654321,
                    'reference' => $reference,
                    'status'    => 'success',
                    'amount'    => $amountKobo,
                    'currency'  => $currency,
                    'customer'  => ['email' => 'buyer@test.com'],
                ],
            ], 200),
        ]);
    }

    /** Pull the decoded items array out of the PendingCheckout for a reference. */
    private function pendingItems(string $reference): array
    {
        return PendingCheckout::where('reference', $reference)->firstOrFail()->items;
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  Group 1 — saveCheckout(): the trusted snapshot (Paystack source of truth)
    // ═══════════════════════════════════════════════════════════════════════

    /** @test */
    public function save_checkout_stores_the_selected_installation_option_in_the_snapshot()
    {
        $user    = User::factory()->create();
        $product = $this->makeProduct([$this->tvOption()], 50000);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [[
                'product_id'          => $product->id,
                'quantity'            => 1,
                'installation_option' => 'Add TV Installation Service',
            ]],
        ]);

        $res->assertOk();
        $ref = $res->json('reference');
        $this->assertNotNull($ref);

        $item = $this->pendingItems($ref)[0];
        $this->assertSame('Add TV Installation Service', $item['installation_option']);
        $this->assertSame(14500, (int) $item['installation_extra_ngn']);
        $this->assertEquals(64500, $item['effective_price_ngn']);   // 50000 + 14500
        $this->assertEquals(64500, $res->json('total_ngn'));
    }

    /** @test */
    public function save_checkout_stores_null_when_no_installation_option_is_selected()
    {
        $user    = User::factory()->create();
        $product = $this->makeProduct([$this->tvOption()], 50000);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [[
                'product_id'          => $product->id,
                'quantity'            => 1,
                'installation_option' => null,
            ]],
        ]);

        $res->assertOk();
        $item = $this->pendingItems($res->json('reference'))[0];
        $this->assertNull($item['installation_option']);
        $this->assertSame(0, (int) $item['installation_extra_ngn']);
        $this->assertEquals(50000, $res->json('total_ngn'));
    }

    /** @test */
    public function save_checkout_rejects_an_installation_option_that_the_product_does_not_offer()
    {
        $user    = User::factory()->create();
        $product = $this->makeProduct([$this->tvOption()], 50000);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [[
                'product_id'          => $product->id,
                'quantity'            => 1,
                'installation_option' => 'Free Diamond Install',   // not a real option
            ]],
        ]);

        $res->assertStatus(422);
        $this->assertDatabaseMissing('pending_checkouts', ['user_id' => $user->id]);
    }

    /** @test */
    public function save_checkout_derives_the_installation_price_from_the_database_not_the_client()
    {
        // The browser only ever sends product_id + quantity + a label string.
        // The extra price must come from the product's stored options, never the client.
        $user    = User::factory()->create();
        $product = $this->makeProduct([$this->tvOption()], 50000);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [[
                'product_id'             => $product->id,
                'quantity'               => 1,
                'installation_option'    => 'Add TV Installation Service',
                'installation_extra_ngn' => 999999,   // hostile client value — must be ignored
                'effective_price_ngn'    => 1,         // hostile client value — must be ignored
            ]],
        ]);

        $res->assertOk();
        $item = $this->pendingItems($res->json('reference'))[0];
        $this->assertSame(14500, (int) $item['installation_extra_ngn']);   // DB value wins
        $this->assertEquals(64500, $item['effective_price_ngn']);
    }

    /** @test */
    public function save_checkout_handles_a_mix_of_items_with_and_without_options()
    {
        $user  = User::factory()->create();
        $withOpt    = $this->makeProduct([$this->tvOption()], 50000);
        $withoutOpt = $this->makeProduct([], 30000);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [
                ['product_id' => $withOpt->id,    'quantity' => 1, 'installation_option' => 'Add TV Installation Service'],
                ['product_id' => $withoutOpt->id, 'quantity' => 1, 'installation_option' => null],
            ],
        ]);

        $res->assertOk();
        $items = collect($this->pendingItems($res->json('reference')))->keyBy('id');

        $this->assertSame(14500, (int) $items[$withOpt->id]['installation_extra_ngn']);
        $this->assertSame('Add TV Installation Service', $items[$withOpt->id]['installation_option']);
        $this->assertSame(0, (int) $items[$withoutOpt->id]['installation_extra_ngn']);
        $this->assertNull($items[$withoutOpt->id]['installation_option']);
        $this->assertEquals(94500, $res->json('total_ngn'));   // 64500 (TV + install) + 30000 (fan)
    }

    /** @test */
    public function save_checkout_records_the_label_of_a_zero_priced_included_option()
    {
        $user    = User::factory()->create();
        $product = $this->makeProduct($this->premiumOptions(), 20000);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [[
                'product_id'          => $product->id,
                'quantity'            => 1,
                'installation_option' => 'Standard',    // free but still a real choice
            ]],
        ]);

        $res->assertOk();
        $item = $this->pendingItems($res->json('reference'))[0];
        $this->assertSame('Standard', $item['installation_option']);
        $this->assertSame(0, (int) $item['installation_extra_ngn']);
        $this->assertEquals(20000, $res->json('total_ngn'));
    }

    /** @test */
    public function save_checkout_multiplies_the_installation_extra_by_quantity_in_the_total()
    {
        $user    = User::factory()->create();
        $product = $this->makeProduct([$this->tvOption()], 50000);

        $res = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [[
                'product_id'          => $product->id,
                'quantity'            => 3,
                'installation_option' => 'Add TV Installation Service',
            ]],
        ]);

        $res->assertOk();
        $item = $this->pendingItems($res->json('reference'))[0];
        $this->assertSame(14500, (int) $item['installation_extra_ngn']);   // stored per-unit
        $this->assertEquals(193500, $res->json('total_ngn'));              // (50000 + 14500) * 3
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  Group 2 — PaystackOrderService::fulfil(): snapshot → order_items
    // ═══════════════════════════════════════════════════════════════════════

    private function makePending(string $ref, array $items, float $totalNgn): PendingCheckout
    {
        return PendingCheckout::create([
            'reference'      => $ref,
            'user_id'        => null,
            'customer_email' => 'buyer@test.com',
            'total_ngn'      => $totalNgn,
            'items'          => $items,
            'fulfillment'    => ['method' => 'pickup', 'pickup_location' => 'Enugu HQ'],
            'coupon'         => null,
        ]);
    }

    /** @test */
    public function paystack_persists_the_installation_option_from_the_snapshot_onto_order_items()
    {
        $ref = 'ps_inst_' . uniqid();
        $this->makePending($ref, [[
            'id'                     => 1,
            'name'                   => 'Hisense 65" TV',
            'basePriceNgn'           => 50000,
            'effective_price_ngn'    => 64500,
            'quantity'               => 1,
            'image'                  => null,
            'sku'                    => 'TV-65',
            'installation_option'    => 'Add TV Installation Service',
            'installation_extra_ngn' => 14500,
        ]], 64500);
        $this->mockPaystack($ref, 6450000);

        $result = $this->service->fulfil($ref, null);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertDatabaseHas('order_items', [
            'order_id'               => $result['order_id'],
            'name'                   => 'Hisense 65" TV',
            'installation_option'    => 'Add TV Installation Service',
            'installation_extra_ngn' => 14500,
        ]);
    }

    /** @test */
    public function paystack_persists_null_installation_when_the_snapshot_has_none()
    {
        $ref = 'ps_noinst_' . uniqid();
        $this->makePending($ref, [[
            'id'                     => 2,
            'name'                   => 'Standing Fan',
            'basePriceNgn'           => 30000,
            'effective_price_ngn'    => 30000,
            'quantity'               => 1,
            'image'                  => null,
            'sku'                    => 'FAN-1',
            'installation_option'    => null,
            'installation_extra_ngn' => 0,
        ]], 30000);
        $this->mockPaystack($ref, 3000000);

        $result = $this->service->fulfil($ref, null);

        $this->assertTrue($result['success']);
        $item = Order::find($result['order_id'])->items()->first();
        $this->assertNull($item->installation_option);
        $this->assertSame(0, $item->installation_extra_ngn);
    }

    /** @test */
    public function paystack_persists_installation_for_each_of_several_items()
    {
        $ref = 'ps_multi_' . uniqid();
        $this->makePending($ref, [
            [
                'id' => 1, 'name' => 'TV', 'basePriceNgn' => 50000, 'effective_price_ngn' => 64500,
                'quantity' => 1, 'image' => null, 'sku' => 'TV',
                'installation_option' => 'Add TV Installation Service', 'installation_extra_ngn' => 14500,
            ],
            [
                'id' => 2, 'name' => 'AC Unit', 'basePriceNgn' => 80000, 'effective_price_ngn' => 123100,
                'quantity' => 1, 'image' => null, 'sku' => 'AC',
                'installation_option' => 'Floor Standing AC Installation Service', 'installation_extra_ngn' => 43100,
            ],
        ], 187600);
        $this->mockPaystack($ref, 18760000);

        $result = $this->service->fulfil($ref, null);
        $this->assertTrue($result['success']);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $result['order_id'], 'name' => 'TV',
            'installation_option' => 'Add TV Installation Service', 'installation_extra_ngn' => 14500,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $result['order_id'], 'name' => 'AC Unit',
            'installation_option' => 'Floor Standing AC Installation Service', 'installation_extra_ngn' => 43100,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  Group 3 — FULL Paystack flow: save-checkout → fulfil (the regression guard)
    // ═══════════════════════════════════════════════════════════════════════

    /** @test */
    public function paystack_end_to_end_installation_option_survives_from_checkout_to_order()
    {
        // This is the direct guard for the bug that shipped: the option a customer
        // selects on the checkout page must reach the created order.
        $user    = User::factory()->create();
        $product = $this->makeProduct([$this->tvOption()], 50000);

        // 1. Browser saves the checkout (only product_id + qty + label leave the client).
        $save = $this->actingAs($user)->postJson('/paystack/save-checkout', [
            'items' => [[
                'product_id'          => $product->id,
                'quantity'            => 1,
                'installation_option' => 'Add TV Installation Service',
            ]],
        ]);
        $save->assertOk();
        $ref      = $save->json('reference');
        $totalNgn = $save->json('total_ngn');
        $this->assertEquals(64500, $totalNgn);

        // 2. Paystack verifies the payment for exactly that amount.
        $this->mockPaystack($ref, (int) round($totalNgn * 100));

        // 3. Fulfilment creates the order from the trusted snapshot.
        $result = $this->service->fulfil($ref, $user->id);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertDatabaseHas('order_items', [
            'order_id'               => $result['order_id'],
            'product_id'             => $product->id,
            'installation_option'    => 'Add TV Installation Service',
            'installation_extra_ngn' => 14500,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  Group 4 — Stripe: StripeOrderService::fulfil() → order_items
    //  (Full Stripe verification/fraud coverage lives in StripeOrderServiceTest;
    //   here we only assert installation options survive on the Stripe path too.)
    // ═══════════════════════════════════════════════════════════════════════

    /** A StripeOrderService whose Stripe lookup returns a fixed, succeeded intent. */
    private function stripeServiceFor(string $reference, float $totalNgn): \App\Services\StripeOrderService
    {
        $cents = (new \App\Services\StripeOrderService())->expectedUsdCents($totalNgn);
        $intent = [
            'status'          => 'succeeded',
            'currency'        => 'usd',
            'amount'          => $cents,
            'amount_received' => $cents,
            'metadata'        => ['reference' => $reference],
        ];

        return new class($intent) extends \App\Services\StripeOrderService {
            public function __construct(private array $intent) {}
            protected function retrievePaymentIntent(string $paymentIntentId): ?array
            {
                return $this->intent;
            }
        };
    }

    /** @test */
    public function stripe_persists_the_installation_option_onto_order_items()
    {
        $ref = 'stripe_inst_' . uniqid();
        $this->makePending($ref, [[
            'id' => 10, 'name' => 'Hisense 65" TV', 'basePriceNgn' => 50000, 'effective_price_ngn' => 64500,
            'quantity' => 1, 'image' => null, 'sku' => 'TV-65',
            'installation_option' => 'Add TV Installation Service', 'installation_extra_ngn' => 14500,
        ]], 64500);

        $result = $this->stripeServiceFor($ref, 64500)->fulfil($ref, 'pi_inst', null);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertDatabaseHas('order_items', [
            'order_id'               => $result['order_id'],
            'name'                   => 'Hisense 65" TV',
            'installation_option'    => 'Add TV Installation Service',
            'installation_extra_ngn' => 14500,
        ]);
    }

    /** @test */
    public function stripe_persists_null_installation_when_none_selected()
    {
        $ref = 'stripe_noinst_' . uniqid();
        $this->makePending($ref, [[
            'id' => 11, 'name' => 'Standing Fan', 'basePriceNgn' => 30000, 'effective_price_ngn' => 30000,
            'quantity' => 1, 'image' => null, 'sku' => 'FAN-1',
            'installation_option' => null, 'installation_extra_ngn' => 0,
        ]], 30000);

        $result = $this->stripeServiceFor($ref, 30000)->fulfil($ref, 'pi_noinst', null);

        $this->assertTrue($result['success']);
        $item = Order::find($result['order_id'])->items()->first();
        $this->assertNull($item->installation_option);
        $this->assertSame(0, $item->installation_extra_ngn);
    }

    /** @test */
    public function stripe_persists_installation_for_multiple_items()
    {
        $ref = 'stripe_multi_' . uniqid();
        $this->makePending($ref, [
            [
                'id' => 20, 'name' => 'TV', 'basePriceNgn' => 50000, 'effective_price_ngn' => 64500,
                'quantity' => 1, 'image' => null, 'sku' => 'TV',
                'installation_option' => 'Add TV Installation Service', 'installation_extra_ngn' => 14500,
            ],
            [
                'id' => 21, 'name' => 'AC Unit', 'basePriceNgn' => 80000, 'effective_price_ngn' => 80000,
                'quantity' => 1, 'image' => null, 'sku' => 'AC',
                'installation_option' => null, 'installation_extra_ngn' => 0,
            ],
        ], 144500);

        $result = $this->stripeServiceFor($ref, 144500)->fulfil($ref, 'pi_multi', null);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $result['order_id'], 'name' => 'TV',
            'installation_option' => 'Add TV Installation Service', 'installation_extra_ngn' => 14500,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $result['order_id'], 'name' => 'AC Unit',
            'installation_option' => null, 'installation_extra_ngn' => 0,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  Group 5 — /api/installation-options: the options fed to the checkout UI
    // ═══════════════════════════════════════════════════════════════════════

    /** @test */
    public function api_returns_installation_options_for_products_that_have_them()
    {
        $withOpt    = $this->makeProduct([$this->tvOption()], 50000);
        $withoutOpt = $this->makeProduct([], 30000);

        $res = $this->postJson('/api/installation-options', [
            'product_ids' => [$withOpt->id, $withoutOpt->id],
        ]);

        $res->assertOk();
        $this->assertCount(1, $res->json((string) $withOpt->id));
        $this->assertSame('Add TV Installation Service', $res->json((string) $withOpt->id . '.0.label'));
        $this->assertSame([], $res->json((string) $withoutOpt->id));
    }

    /** @test */
    public function api_includes_every_requested_id_even_with_no_options()
    {
        $product = $this->makeProduct([], 30000);

        $res = $this->postJson('/api/installation-options', [
            'product_ids' => [$product->id],
        ]);

        $res->assertOk();
        $this->assertIsArray($res->json((string) $product->id));
        $this->assertSame([], $res->json((string) $product->id));
    }
}
