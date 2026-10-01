<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * The invoice settings form pre-fills the default Terms & Footer text so it's
 * clear what the invoice will show; saved custom text takes precedence.
 */
class AdminInvoiceSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::clearCache();
    }

    protected function tearDown(): void
    {
        Setting::clearCache();
        parent::tearDown();
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->role = 'admin';
        $u->save();
        return $u;
    }

    /** @test */
    public function terms_and_footer_are_prefilled_with_defaults_when_unset()
    {
        $this->actingAs($this->admin())
            ->get(route('admin.invoice.settings'))
            ->assertOk()
            ->assertSee('This invoice is evidence of your order')  // default terms
            ->assertSee('Thank you for shopping with');            // default footer
    }

    /** @test */
    public function saved_custom_terms_take_precedence_over_the_default()
    {
        Setting::set('inv_terms', 'Custom terms line for this store.');

        // The textarea is pre-filled with the saved custom terms, not the default.
        $this->actingAs($this->admin())
            ->get(route('admin.invoice.settings'))
            ->assertOk()
            ->assertSee('<textarea class="inv-ta" name="inv_terms" id="inv_terms" rows="8">Custom terms line for this store.</textarea>', false);
    }

    /** @test */
    public function admin_can_save_invoice_terms()
    {
        $this->actingAs($this->admin())
            ->put(route('admin.invoice.settings.update'), [
                'inv_terms'  => 'Returns accepted within 7 days.',
                'inv_footer' => 'Powered by Albertina.',
            ])
            ->assertRedirect();

        Setting::clearCache();
        $this->assertSame('Returns accepted within 7 days.', Setting::get('inv_terms'));
        $this->assertSame('Powered by Albertina.', Setting::get('inv_footer'));
    }
}
