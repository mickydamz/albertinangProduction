<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * User-side order lifecycle AFTER an order exists: listing, invoice view,
 * invoice download, and return requests — plus the ownership guards that keep
 * one customer out of another's orders.
 *
 * Each behaviour is exercised for both payment gateways (paystack + stripe) to
 * confirm nothing in the post-order flow is gateway-specific.
 */
class OrderUserLifecycleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function gateways(): array
    {
        return [
            'paystack' => ['paystack'],
            'stripe'   => ['stripe'],
        ];
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function makeOrder(User $user, string $gateway, array $overrides = []): Order
    {
        return Order::create(array_merge([
            'user_id'        => $user->id,
            'status'         => 'delivered',
            'total'          => 50000,
            'total_usd'      => 32.50,
            'payment_method' => $gateway,
            'reference'      => $gateway . '_ref_' . uniqid(),
            'payment_id'     => 'pi_' . uniqid(),
            'customer_email' => $user->email,
        ], $overrides));
    }

    // ── Listing ───────────────────────────────────────────────────────────────

    /**
     * @test
     * @dataProvider gateways
     */
    public function a_user_sees_only_their_own_orders_in_the_account_list(string $gateway)
    {
        $me    = User::factory()->create();
        $other = User::factory()->create();

        $mine     = $this->makeOrder($me, $gateway);
        $theirs   = $this->makeOrder($other, $gateway);

        $response = $this->actingAs($me)->get(route('account.orders'));

        $response->assertOk();
        $response->assertSee($mine->order_number);
        $response->assertDontSee($theirs->order_number);
    }

    // ── Invoice (view) ──────────────────────────────────────────────────────

    /**
     * @test
     * @dataProvider gateways
     */
    public function a_user_can_view_the_invoice_for_their_own_order(string $gateway)
    {
        $me    = User::factory()->create();
        $order = $this->makeOrder($me, $gateway);

        $response = $this->actingAs($me)->get(route('account.orders.invoice', $order));

        $response->assertOk();
        $response->assertSee($order->order_number);
    }

    /**
     * @test
     * @dataProvider gateways
     */
    public function a_user_cannot_view_another_users_invoice(string $gateway)
    {
        $owner    = User::factory()->create();
        $attacker = User::factory()->create();
        $order    = $this->makeOrder($owner, $gateway);

        $this->actingAs($attacker)
            ->get(route('account.orders.invoice', $order))
            ->assertForbidden();
    }

    // ── Invoice (download) ────────────────────────────────────────────────────

    /**
     * @test
     * @dataProvider gateways
     */
    public function a_user_can_download_the_pdf_invoice_for_their_own_order(string $gateway)
    {
        $me    = User::factory()->create();
        $order = $this->makeOrder($me, $gateway);

        $response = $this->actingAs($me)->get(route('account.orders.invoice.download', $order));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    /**
     * @test
     * @dataProvider gateways
     */
    public function a_user_cannot_download_another_users_invoice(string $gateway)
    {
        $owner    = User::factory()->create();
        $attacker = User::factory()->create();
        $order    = $this->makeOrder($owner, $gateway);

        $this->actingAs($attacker)
            ->get(route('account.orders.invoice.download', $order))
            ->assertForbidden();
    }

    // ── Returns ────────────────────────────────────────────────────────────────

    /**
     * @test
     * @dataProvider gateways
     */
    public function a_user_can_request_a_return_on_their_own_order(string $gateway)
    {
        $me    = User::factory()->create();
        $order = $this->makeOrder($me, $gateway);

        $response = $this->actingAs($me)->post(
            route('account.orders.return.submit', $order),
            ['reason' => 'The item arrived damaged and I would like to return it for a refund.']
        );

        $response->assertRedirect(route('account.orders'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('order_returns', [
            'order_id' => $order->id,
            'user_id'  => $me->id,
            'status'   => 'pending',
        ]);
    }

    /**
     * @test
     * @dataProvider gateways
     */
    public function a_user_cannot_request_a_return_on_another_users_order(string $gateway)
    {
        $owner    = User::factory()->create();
        $attacker = User::factory()->create();
        $order    = $this->makeOrder($owner, $gateway);

        $this->actingAs($attacker)
            ->post(route('account.orders.return.submit', $order), [
                'reason' => 'Trying to open a return on an order that is not mine at all.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('order_returns', ['order_id' => $order->id]);
    }

    /** @test */
    public function a_return_reason_must_meet_the_minimum_length()
    {
        $me    = User::factory()->create();
        $order = $this->makeOrder($me, 'paystack');

        $this->actingAs($me)
            ->post(route('account.orders.return.submit', $order), ['reason' => 'too short'])
            ->assertSessionHasErrors('reason');

        $this->assertDatabaseMissing('order_returns', ['order_id' => $order->id]);
    }

    // ── Return / cancel pages (ownership on GET) ────────────────────────────────

    /** @test */
    public function a_user_cannot_open_the_return_or_cancel_page_for_another_users_order()
    {
        $owner    = User::factory()->create();
        $attacker = User::factory()->create();
        $order    = $this->makeOrder($owner, 'stripe');

        $this->actingAs($attacker)->get(route('account.orders.return', $order))->assertForbidden();
        $this->actingAs($attacker)->get(route('account.orders.cancel', $order))->assertForbidden();
    }

    // ── Auth ────────────────────────────────────────────────────────────────────

    /** @test */
    public function a_guest_is_redirected_to_login_from_the_orders_area()
    {
        $this->get(route('account.orders'))->assertRedirect(route('login'));
    }
}
