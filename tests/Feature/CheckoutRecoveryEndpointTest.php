<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PendingCheckout;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Customer-facing checkout recovery (CheckoutRecoveryController): the owner can
 * recover a paid-but-orderless checkout, the action is idempotent (never creates a
 * duplicate order), and another customer can neither view nor recover it.
 */
class CheckoutRecoveryEndpointTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();
    }

    private function makePending(User $owner): PendingCheckout
    {
        return PendingCheckout::create([
            'reference'      => 'ps_rec_' . uniqid(),
            'user_id'        => $owner->id,
            'customer_email' => $owner->email,
            'total_ngn'      => 50000,
            'items'          => [[
                'id' => 1, 'name' => 'TV', 'basePriceNgn' => 50000, 'effective_price_ngn' => 50000,
                'quantity' => 1, 'image' => null, 'sku' => 'TV-1',
                'installation_option' => null, 'installation_extra_ngn' => 0,
            ]],
            'fulfillment'    => ['method' => 'pickup', 'pickup_location' => 'HQ'],
            'coupon'         => null,
        ]);
    }

    private function mockPaystack(string $reference): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status'  => true,
                'message' => 'Verification successful',
                'data'    => [
                    'id' => 77778888, 'reference' => $reference, 'status' => 'success',
                    'amount' => 5000000, 'currency' => 'NGN',
                    'customer' => ['email' => 'buyer@test.com'],
                ],
            ], 200),
        ]);
    }

    /** @test */
    public function the_owner_can_recover_a_checkout_into_an_order()
    {
        $owner    = User::factory()->create();
        $checkout = $this->makePending($owner);
        $this->mockPaystack($checkout->reference);

        $this->actingAs($owner)->post(route('checkout.recover', $checkout->reference))->assertRedirect();

        $this->assertDatabaseHas('orders', ['reference' => $checkout->reference, 'status' => 'paid', 'user_id' => $owner->id]);
    }

    /** @test */
    public function recovery_is_idempotent_and_never_creates_a_duplicate_order()
    {
        $owner    = User::factory()->create();
        $checkout = $this->makePending($owner);
        $this->mockPaystack($checkout->reference);

        $this->actingAs($owner)->post(route('checkout.recover', $checkout->reference))->assertRedirect();
        $this->actingAs($owner)->post(route('checkout.recover', $checkout->reference))->assertRedirect();

        $this->assertSame(1, Order::where('reference', $checkout->reference)->count());
    }

    /** @test */
    public function another_customer_cannot_recover_someone_elses_checkout()
    {
        $owner    = User::factory()->create();
        $checkout = $this->makePending($owner);
        $intruder = User::factory()->create();
        Http::fake(); // nothing should be sent

        $this->actingAs($intruder)->post(route('checkout.recover', $checkout->reference))->assertForbidden();

        $this->assertDatabaseMissing('orders', ['reference' => $checkout->reference]);
        Http::assertNothingSent();
    }

    /** @test */
    public function another_customer_cannot_view_the_recovery_page()
    {
        $owner    = User::factory()->create();
        $checkout = $this->makePending($owner);
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get(route('checkout.recovery', $checkout->reference))->assertForbidden();
    }

    /** @test */
    public function a_guest_is_redirected_to_login()
    {
        $owner    = User::factory()->create();
        $checkout = $this->makePending($owner);

        $this->post(route('checkout.recover', $checkout->reference))->assertRedirect(route('login'));
    }
}
