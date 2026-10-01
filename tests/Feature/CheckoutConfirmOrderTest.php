<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\User;
use App\Services\PaystackOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Tests for POST /paystack/confirm-order  (OrderController::verifyAndStorePaystackOrder)
 *
 * This is the browser-side Paystack confirmation path. Current contract:
 *   - Requires authentication (route is behind the `auth` middleware).
 *   - Reads ONLY `reference` from the request body — nothing else is trusted.
 *   - Delegates to PaystackOrderService::fulfil(), which builds the order from
 *     the server-side PendingCheckout snapshot (the same source the webhook uses).
 *
 * So every success case needs: an authenticated user + a PendingCheckout for the
 * reference + a verified Paystack transaction whose amount covers the snapshot total.
 */
class CheckoutConfirmOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** Create the trusted checkout snapshot the service reads from. */
    private function makePending(string $reference, ?int $userId, array $overrides = []): PendingCheckout
    {
        return PendingCheckout::create(array_merge([
            'reference'      => $reference,
            'user_id'        => $userId,
            'customer_email' => 'buyer@test.com',
            'total_ngn'      => 80000,
            'items'          => [[
                'id'                     => 10,
                'name'                   => 'Standing Fan',
                'basePriceNgn'           => 80000,
                'effective_price_ngn'    => 80000,
                'quantity'               => 1,
                'image'                  => null,
                'sku'                    => 'FAN-001',
                'installation_option'    => null,
                'installation_extra_ngn' => 0,
            ]],
            'fulfillment'    => ['method' => 'pickup', 'pickup_location' => 'Enugu HQ'],
            'coupon'         => null,
        ], $overrides));
    }

    private function mockPaystackOk(string $reference, int $amountKobo = 8000000): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data'   => [
                    'id'        => 111,
                    'reference' => $reference,
                    'status'    => 'success',
                    'amount'    => $amountKobo,
                    'currency'  => 'NGN',
                    'customer'  => ['email' => 'buyer@test.com'],
                ],
            ], 200),
        ]);
    }

    private function mockPaystackFail(string $reference): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status'  => false,
                'message' => 'Transaction not found',
                'data'    => ['reference' => $reference, 'status' => 'failed'],
            ], 400),
        ]);
    }

    // ── Success paths ─────────────────────────────────────────────────────────

    /** @test */
    public function it_creates_order_and_returns_success_for_authenticated_user()
    {
        $user = User::factory()->create();
        $ref  = 'ps_auth_' . uniqid();
        $this->makePending($ref, $user->id);
        $this->mockPaystackOk($ref);

        $response = $this->actingAs($user)
            ->postJson('/paystack/confirm-order', ['reference' => $ref]);

        $response->assertOk()->assertJson([
            'success' => true,
            'status'  => 'success',
        ]);

        $this->assertDatabaseHas('orders', [
            'reference'      => $ref,
            'user_id'        => $user->id,
            'payment_method' => 'paystack',
        ]);
    }

    /** @test */
    public function it_saves_delivery_fulfillment_fields_from_the_snapshot()
    {
        $user = User::factory()->create();
        $ref  = 'ps_del_' . uniqid();
        $this->makePending($ref, $user->id, [
            'fulfillment' => [
                'method'                 => 'delivery',
                'delivery_state_name'    => 'Rivers',
                'delivery_location_name' => 'Port Harcourt',
                'delivery_fee_ngn'       => 4000,
            ],
        ]);
        $this->mockPaystackOk($ref);

        $response = $this->actingAs($user)
            ->postJson('/paystack/confirm-order', ['reference' => $ref]);

        $response->assertOk();
        $this->assertDatabaseHas('orders', [
            'reference'           => $ref,
            'fulfillment_method'  => 'delivery',
            'delivery_state_name' => 'Rivers',
            'shipping_cost'       => 4000,
        ]);
    }

    /** @test */
    public function it_returns_success_with_duplicate_flag_for_existing_reference()
    {
        $user = User::factory()->create();
        $ref  = 'ps_dupbrowser_' . uniqid();
        $this->makePending($ref, $user->id);
        $this->mockPaystackOk($ref);

        // First confirm creates the order.
        $this->actingAs($user)->postJson('/paystack/confirm-order', ['reference' => $ref]);

        // Second confirm (e.g. user double-clicks) must be idempotent.
        $response = $this->actingAs($user)
            ->postJson('/paystack/confirm-order', ['reference' => $ref]);

        $response->assertOk()->assertJson(['success' => true, 'duplicate' => true]);
        $this->assertEquals(1, Order::where('reference', $ref)->count());
    }

    // ── Failure paths ───────────────────────────────────────────────────────

    /** @test */
    public function it_rejects_unauthenticated_requests()
    {
        $ref = 'ps_noauth_' . uniqid();
        $this->makePending($ref, null);
        $this->mockPaystackOk($ref);

        // No actingAs() — the route is behind the auth middleware.
        $response = $this->postJson('/paystack/confirm-order', ['reference' => $ref]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_returns_failure_when_paystack_verification_fails()
    {
        $user = User::factory()->create();
        $ref  = 'ps_fail_' . uniqid();
        $this->makePending($ref, $user->id);
        $this->mockPaystackFail($ref);

        $response = $this->actingAs($user)
            ->postJson('/paystack/confirm-order', ['reference' => $ref]);

        $response->assertStatus(400)->assertJson(['success' => false]);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_returns_failure_when_no_pending_checkout_exists()
    {
        // Payment verifies, but there is no trusted snapshot — the service must
        // refuse to invent an order from thin air.
        $user = User::factory()->create();
        $ref  = 'ps_nopending_' . uniqid();
        $this->mockPaystackOk($ref);

        $response = $this->actingAs($user)
            ->postJson('/paystack/confirm-order', ['reference' => $ref]);

        $response->assertStatus(400)->assertJson(['success' => false]);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_rejects_underpayment_as_fraud()
    {
        $user = User::factory()->create();
        $ref  = 'ps_under_' . uniqid();
        $this->makePending($ref, $user->id, ['total_ngn' => 80000]); // expects 8,000,000 kobo
        $this->mockPaystackOk($ref, 10000);                          // paid only ₦100

        $response = $this->actingAs($user)
            ->postJson('/paystack/confirm-order', ['reference' => $ref]);

        $response->assertStatus(400)->assertJson(['success' => false]);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_validates_required_reference_field()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/paystack/confirm-order', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['reference']);
    }

    /** @test */
    public function it_returns_a_meaningful_error_not_404_when_the_service_fails()
    {
        // If fulfilment fails, the user must get a proper error (payment was taken),
        // never a bare 404 that looks like the endpoint is missing.
        $user = User::factory()->create();
        $ref  = 'ps_dberr_' . uniqid();

        $this->app->instance(PaystackOrderService::class, new class extends PaystackOrderService {
            public function fulfil(string $reference, ?int $userId): array
            {
                return [
                    'success'      => false,
                    'order_id'     => null,
                    'order_number' => null,
                    'duplicate'    => false,
                    'error_type'   => 'transient',
                    'message'      => 'DB connection failed',
                ];
            }
        });

        $response = $this->actingAs($user)
            ->postJson('/paystack/confirm-order', ['reference' => $ref]);

        $this->assertNotEquals(404, $response->status());
        $response->assertJson(['success' => false]);
        $this->assertNotEmpty($response->json('message'));
    }
}
