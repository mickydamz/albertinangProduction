<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\User;
use App\Services\StripeOrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Coverage for the hardened Stripe path (StripeOrderService::fulfil).
 *
 * The service verifies the PaymentIntent with Stripe and builds the order from
 * the trusted PendingCheckout snapshot — the browser cannot supply prices,
 * totals, or items. Tests stub the Stripe network call via a subclass so no
 * request ever leaves the machine.
 */
class StripeOrderServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** A service whose Stripe lookup returns a fixed PaymentIntent array. */
    private function serviceReturning(?array $intent): StripeOrderService
    {
        return new class($intent) extends StripeOrderService {
            public function __construct(private ?array $intent) {}
            protected function retrievePaymentIntent(string $paymentIntentId): ?array
            {
                return $this->intent;
            }
        };
    }

    /** Build a succeeded PaymentIntent array for the given reference + cents. */
    private function intent(string $reference, int $cents, array $overrides = []): array
    {
        return array_merge([
            'status'          => 'succeeded',
            'currency'        => 'usd',
            'amount'          => $cents,
            'amount_received' => $cents,
            'metadata'        => ['reference' => $reference],
        ], $overrides);
    }

    private function makePending(string $ref, array $items, float $totalNgn, ?int $userId = null): PendingCheckout
    {
        return PendingCheckout::create([
            'reference'      => $ref,
            'user_id'        => $userId,
            'customer_email' => 'buyer@test.com',
            'total_ngn'      => $totalNgn,
            'items'          => $items,
            'fulfillment'    => ['method' => 'pickup', 'pickup_location' => 'Enugu HQ'],
            'coupon'         => null,
        ]);
    }

    private function item(array $overrides = []): array
    {
        return array_merge([
            'id'                     => 5,
            'name'                   => 'Microwave',
            'basePriceNgn'           => 50000,
            'effective_price_ngn'    => 50000,
            'quantity'               => 1,
            'image'                  => null,
            'sku'                    => 'MW-1',
            'installation_option'    => null,
            'installation_extra_ngn' => 0,
        ], $overrides);
    }

    // ── Success ─────────────────────────────────────────────────────────────

    /** @test */
    public function it_creates_a_paid_stripe_order_from_the_snapshot()
    {
        $ref     = 'stripe_ok_' . uniqid();
        $this->makePending($ref, [$this->item()], 50000);
        $svc     = new StripeOrderService();
        $cents   = $svc->expectedUsdCents(50000);
        $service = $this->serviceReturning($this->intent($ref, $cents));

        $result = $service->fulfil($ref, 'pi_ABC', null);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertDatabaseHas('orders', [
            'reference'      => $ref,
            'payment_id'     => 'pi_ABC',
            'payment_method' => 'stripe',
            'status'         => 'paid',
        ]);
        // fulfilment must be recorded so the intent can't be reused.
        $this->assertNotNull(PendingCheckout::where('reference', $ref)->value('fulfilled_at'));
    }

    /** @test */
    public function it_persists_installation_options_onto_stripe_order_items()
    {
        $ref = 'stripe_inst_' . uniqid();
        $this->makePending($ref, [$this->item([
            'name'                   => 'Hisense 65" TV',
            'effective_price_ngn'    => 64500,
            'installation_option'    => 'Add TV Installation Service',
            'installation_extra_ngn' => 14500,
        ])], 64500);
        $svc     = new StripeOrderService();
        $service = $this->serviceReturning($this->intent($ref, $svc->expectedUsdCents(64500)));

        $result = $service->fulfil($ref, 'pi_inst', null);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('order_items', [
            'order_id'               => $result['order_id'],
            'name'                   => 'Hisense 65" TV',
            'installation_option'    => 'Add TV Installation Service',
            'installation_extra_ngn' => 14500,
        ]);
    }

    // ── Fraud / verification failures ────────────────────────────────────────

    /** @test */
    public function it_rejects_underpayment()
    {
        $ref = 'stripe_under_' . uniqid();
        $this->makePending($ref, [$this->item()], 50000);
        $svc     = new StripeOrderService();
        $short   = $svc->expectedUsdCents(50000) - 100;   // paid $1 less than owed
        $service = $this->serviceReturning($this->intent($ref, $short));

        $result = $service->fulfil($ref, 'pi_under', null);

        $this->assertFalse($result['success']);
        $this->assertSame('fraud', $result['error_type']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_rejects_wrong_currency()
    {
        $ref = 'stripe_curr_' . uniqid();
        $this->makePending($ref, [$this->item()], 50000);
        $svc     = new StripeOrderService();
        $service = $this->serviceReturning($this->intent($ref, $svc->expectedUsdCents(50000), ['currency' => 'ngn']));

        $result = $service->fulfil($ref, 'pi_curr', null);

        $this->assertFalse($result['success']);
        $this->assertSame('fraud', $result['error_type']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_rejects_a_payment_intent_stamped_with_a_different_reference()
    {
        // An attacker tries to satisfy an expensive order with a cheap intent
        // from another checkout. The metadata reference must match.
        $ref = 'stripe_meta_' . uniqid();
        $this->makePending($ref, [$this->item()], 50000);
        $svc     = new StripeOrderService();
        $intent  = $this->intent($ref, $svc->expectedUsdCents(50000), ['metadata' => ['reference' => 'someone_elses_ref']]);
        $service = $this->serviceReturning($intent);

        $result = $service->fulfil($ref, 'pi_meta', null);

        $this->assertFalse($result['success']);
        $this->assertSame('fraud', $result['error_type']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_rejects_when_the_payment_has_not_succeeded()
    {
        $ref = 'stripe_notdone_' . uniqid();
        $this->makePending($ref, [$this->item()], 50000);
        $svc     = new StripeOrderService();
        $service = $this->serviceReturning($this->intent($ref, $svc->expectedUsdCents(50000), ['status' => 'requires_payment_method']));

        $result = $service->fulfil($ref, 'pi_notdone', null);

        $this->assertFalse($result['success']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    /** @test */
    public function it_returns_failure_when_no_pending_checkout_exists()
    {
        $ref     = 'stripe_nopending_' . uniqid();
        $svc     = new StripeOrderService();
        $service = $this->serviceReturning($this->intent($ref, 5000));

        $result = $service->fulfil($ref, 'pi_nopending', null);

        $this->assertFalse($result['success']);
        $this->assertDatabaseMissing('orders', ['reference' => $ref]);
    }

    // ── Idempotency ───────────────────────────────────────────────────────────

    /** @test */
    public function it_is_idempotent_when_confirmed_twice()
    {
        $ref = 'stripe_dup_' . uniqid();
        $this->makePending($ref, [$this->item()], 50000);
        $svc     = new StripeOrderService();
        $cents   = $svc->expectedUsdCents(50000);

        $first  = $this->serviceReturning($this->intent($ref, $cents))->fulfil($ref, 'pi_dup', null);
        $this->assertTrue($first['success']);
        $this->assertFalse($first['duplicate']);

        // Second confirm (double-click / retry) must not create a second order.
        $second = $this->serviceReturning($this->intent($ref, $cents))->fulfil($ref, 'pi_dup', null);
        $this->assertTrue($second['success']);
        $this->assertTrue($second['duplicate']);
        $this->assertEquals(1, Order::where('reference', $ref)->count());
    }

    // ── Key configuration guard ────────────────────────────────────────────────

    /** @test */
    public function it_detects_a_missing_or_placeholder_stripe_key()
    {
        $this->assertFalse(StripeOrderService::keyConfigured(null));
        $this->assertFalse(StripeOrderService::keyConfigured(''));
        $this->assertFalse(StripeOrderService::keyConfigured('sk_test_REPLACE_WITH_YOUR_STRIPE_TEST_SECRET_KEY'));
        $this->assertTrue(StripeOrderService::keyConfigured('sk_live_realkey123'));
    }

    /** @test */
    public function create_payment_intent_degrades_gracefully_when_key_not_configured()
    {
        // The testing env ships the placeholder key, so the endpoint must refuse
        // cleanly (503) instead of throwing a raw Stripe error at the customer.
        $this->withoutMiddleware(ThrottleRequests::class);
        config(['services.stripe.secret' => 'sk_test_REPLACE_WITH_YOUR_STRIPE_TEST_SECRET_KEY']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/stripe/create-payment-intent', ['reference' => 'anything']);

        $response->assertStatus(503)->assertJsonStructure(['error']);
    }
}
