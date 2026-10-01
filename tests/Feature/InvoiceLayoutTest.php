<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminInvoiceSettingsController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Admin-configurable invoice layout, per fulfilment mode.
 *
 * The admin can design two separate layouts (pickup vs delivery): the header
 * element positions and the top-to-bottom order of body sections. Those choices
 * must (a) persist per mode, (b) resolve back correctly for each mode, and
 * (c) actually drive the order of sections on the real rendered invoice.
 */
class InvoiceLayoutTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->role = 'admin';
        $u->save();
        return $u;
    }

    private function layoutJson(array $body): string
    {
        return json_encode([
            'hdr'  => AdminInvoiceSettingsController::DEFAULT_HDR,
            'body' => $body,
        ]);
    }

    // ── Editor renders (WYSIWYG scaffolding) ───────────────────────────────────

    /** @test */
    public function the_invoice_settings_editor_renders_with_the_wysiwyg_preview()
    {
        $res = $this->actingAs($this->admin())->get('/admin/invoice-settings');

        $res->assertOk();
        // Both per-mode hidden inputs are present.
        $res->assertSee('name="inv_layout_pickup"', false);
        $res->assertSee('name="inv_layout_delivery"', false);
        // The preview paper and the real-invoice classes it mirrors are present.
        $res->assertSee('id="invPaper"', false);
        $res->assertSee('inv-info-tbl', false);
        $res->assertSee('inv-green-bar', false);
    }

    // ── Persistence ───────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_save_a_separate_layout_for_pickup_and_delivery()
    {
        $pickupBody   = ['items', 'bill_to', 'fulfillment', 'notes_totals', 'bank', 'terms', 'footer'];
        $deliveryBody = ['bill_to', 'fulfillment', 'items', 'notes_totals', 'terms', 'bank', 'footer'];

        $res = $this->actingAs($this->admin())->put('/admin/invoice-settings', [
            'inv_accent_color'    => '#123456',
            'inv_layout_pickup'   => $this->layoutJson($pickupBody),
            'inv_layout_delivery' => $this->layoutJson($deliveryBody),
        ]);

        $res->assertRedirect();
        Setting::clearCache();

        $this->assertSame($pickupBody,   AdminInvoiceSettingsController::templateVars('pickup')['invLayout']['body']);
        $this->assertSame($deliveryBody, AdminInvoiceSettingsController::templateVars('delivery')['invLayout']['body']);
    }

    // ── Resolution + normalisation ─────────────────────────────────────────────

    /** @test */
    public function template_vars_default_when_nothing_is_saved()
    {
        Setting::clearCache();
        $vars = AdminInvoiceSettingsController::templateVars('pickup');

        $this->assertSame(AdminInvoiceSettingsController::DEFAULT_BODY, $vars['invLayout']['body']);
        $this->assertArrayHasKey('logo', $vars['invLayout']['hdr']);
        $this->assertSame('pickup', $vars['invMode']);
    }

    /** @test */
    public function a_saved_layout_is_normalised_dropping_unknown_and_appending_missing_sections()
    {
        // Only two valid sections listed (plus a bogus one); the rest must be
        // appended so nothing silently vanishes from the invoice.
        Setting::set('inv_layout_delivery', json_encode([
            'body' => ['footer', 'items', 'not_a_real_section'],
        ]));
        Setting::clearCache();

        $body = AdminInvoiceSettingsController::templateVars('delivery')['invLayout']['body'];

        // Bogus section dropped.
        $this->assertNotContains('not_a_real_section', $body);
        // Explicit ones kept, in order, at the front.
        $this->assertSame('footer', $body[0]);
        $this->assertSame('items',  $body[1]);
        // Everything else present exactly once.
        foreach (AdminInvoiceSettingsController::DEFAULT_BODY as $sec) {
            $this->assertContains($sec, $body);
            $this->assertSame(1, count(array_keys($body, $sec)));
        }
    }

    /** @test */
    public function it_falls_back_to_the_legacy_single_layout_when_no_per_mode_layout_exists()
    {
        $legacyBody = ['terms', 'footer', 'bill_to', 'fulfillment', 'items', 'notes_totals', 'bank'];
        Setting::set('inv_layout', $this->layoutJson($legacyBody));
        Setting::clearCache();

        // Neither pickup nor delivery layout saved → both inherit the legacy one.
        $this->assertSame($legacyBody, AdminInvoiceSettingsController::templateVars('pickup')['invLayout']['body']);
        $this->assertSame($legacyBody, AdminInvoiceSettingsController::templateVars('delivery')['invLayout']['body']);
    }

    // ── The payoff: the rendered invoice honours the saved order ───────────────

    /** @test */
    public function the_rendered_invoice_orders_sections_per_the_saved_pickup_layout()
    {
        // Put Terms before Bill To for pickup orders.
        Setting::set('inv_layout_pickup', $this->layoutJson(
            ['terms', 'bill_to', 'fulfillment', 'items', 'notes_totals', 'bank', 'footer']
        ));
        Setting::clearCache();

        $order = $this->makePickupOrder();

        $html = $this->actingAs($this->admin())
            ->get("/admin/orders/{$order->id}/invoice")
            ->assertOk()
            ->getContent();

        $termsPos  = strpos($html, 'Terms &amp; Conditions');
        $billToPos = strpos($html, 'Bill To');

        $this->assertNotFalse($termsPos);
        $this->assertNotFalse($billToPos);
        $this->assertLessThan($billToPos, $termsPos, 'Terms should render before Bill To for the saved pickup layout');
    }

    /** @test */
    public function the_rendered_invoice_uses_the_default_order_when_unconfigured()
    {
        Setting::clearCache();
        $order = $this->makePickupOrder();

        $html = $this->actingAs($this->admin())
            ->get("/admin/orders/{$order->id}/invoice")
            ->assertOk()
            ->getContent();

        // Default order has Bill To before Terms.
        $this->assertLessThan(strpos($html, 'Terms &amp; Conditions'), strpos($html, 'Bill To'));
    }

    /** @test */
    public function the_pdf_invoice_renders_with_a_configured_layout()
    {
        Setting::set('inv_layout_pickup', $this->layoutJson(
            ['items', 'bill_to', 'fulfillment', 'notes_totals', 'bank', 'terms', 'footer']
        ));
        Setting::clearCache();

        $order = $this->makePickupOrder();

        $res = $this->actingAs($this->admin())->get("/admin/orders/{$order->id}/invoice/download");

        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('content-type'));
    }

    private function makePickupOrder(): Order
    {
        $user    = User::factory()->create();
        $product = Product::create([
            'name' => 'Invoice Product ' . uniqid(), 'price' => 50000, 'stock' => 5, 'is_active' => true,
        ]);

        $order = Order::create([
            'user_id'            => $user->id,
            'status'             => 'completed',
            'fulfillment_method' => 'pickup',
            'total'              => 50000,
            'shipping_cost'      => 0,
            'order_number'       => 'ALB-' . uniqid(),
            'payment_method'     => 'paystack',
            'pickup_point_name'  => 'Garki Collection Point',
        ]);
        OrderItem::create([
            'order_id'               => $order->id,
            'product_id'             => $product->id,
            'name'                   => $product->name,
            'price'                  => 50000,
            'quantity'               => 1,
            'installation_extra_ngn' => 0,
        ]);

        return $order->load('items', 'user');
    }
}
