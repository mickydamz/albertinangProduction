<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin store settings (AdminSettingsController) and shipping-cost editor
 * (AdminShippingController).
 */
class AdminSettingsShippingTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Setting::clearCache();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    protected function tearDown(): void
    {
        Setting::clearCache();
        parent::tearDown();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // Note: that a settings PUT persists is already covered by
    // AdminEmailVerificationSettingTest; here we only add the store_name
    // required-validation and the shipping-cost editor (a separate controller).

    /** @test */
    public function store_settings_require_a_store_name()
    {
        $this->actingAs($this->admin())->from('/admin/settings')
            ->put('/admin/settings', [])
            ->assertSessionHasErrors('store_name');
    }

    /** @test */
    public function an_admin_can_update_a_locations_shipping_cost()
    {
        $loc = Location::create(['name' => 'Ibadan', 'is_active' => true, 'shipping_cost' => 0]);

        $this->actingAs($this->admin())->from('/admin/shipping')->put('/admin/shipping', [
            'shipping' => [$loc->id => 2500],
        ])->assertRedirect();

        $this->assertEquals(2500, (float) $loc->fresh()->shipping_cost);
    }
}
