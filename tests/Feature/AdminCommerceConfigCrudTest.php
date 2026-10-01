<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin commerce config CRUD: coupons, currencies and payment methods.
 */
class AdminCommerceConfigCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ── Coupons ─────────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_update_and_delete_a_coupon()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/coupons', [
            'code'          => 'save10',
            'discount_type' => 'percent',
            'value'         => 10,
            'is_active'     => 1,
        ])->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'SAVE10', 'discount_type' => 'percent']);

        $coupon = Coupon::where('code', 'SAVE10')->first();

        $this->actingAs($admin)->put("/admin/coupons/{$coupon->id}", [
            'code'          => 'SAVE10',
            'discount_type' => 'fixed',
            'value'         => 2000,
            'is_active'     => 1,
        ])->assertRedirect();
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'discount_type' => 'fixed']);

        $this->actingAs($admin)->delete("/admin/coupons/{$coupon->id}")->assertRedirect();
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    /** @test */
    public function a_percentage_coupon_cannot_exceed_100()
    {
        $this->actingAs($this->admin())->from('/admin/coupons/create')->post('/admin/coupons', [
            'code'          => 'HUGE',
            'discount_type' => 'percent',
            'value'         => 150,
        ])->assertSessionHasErrors('value');

        $this->assertDatabaseMissing('coupons', ['code' => 'HUGE']);
    }

    // ── Currencies ──────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_a_currency()
    {
        $this->actingAs($this->admin())->post('/admin/currencies', [
            'code'        => 'GBP',
            'symbol'      => '£',
            'name'        => 'British Pound',
            'rate_to_ngn' => 0.00053,
        ])->assertRedirect(route('admin.currencies.index'));

        $this->assertDatabaseHas('currencies', ['code' => 'GBP']);
    }

    /** @test */
    public function the_base_currency_cannot_be_deleted()
    {
        $base = Currency::create([
            'code' => 'NGN', 'symbol' => '₦', 'name' => 'Naira',
            'rate_to_ngn' => 1, 'is_base' => true, 'is_active' => true, 'sort_order' => 0,
        ]);

        $this->actingAs($this->admin())->from('/admin/currencies')
            ->delete("/admin/currencies/{$base->id}");

        $this->assertDatabaseHas('currencies', ['id' => $base->id]); // still there
    }

    /** @test */
    public function an_admin_can_delete_a_non_base_currency()
    {
        $cur = Currency::create([
            'code' => 'CAD', 'symbol' => 'C$', 'name' => 'Canadian Dollar',
            'rate_to_ngn' => 0.001, 'is_base' => false, 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->actingAs($this->admin())->delete("/admin/currencies/{$cur->id}")->assertRedirect();
        $this->assertDatabaseMissing('currencies', ['id' => $cur->id]);
    }

    // ── Payment methods ─────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_and_delete_a_payment_method()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/payment-methods', [
            'name'    => 'Bank Transfer',
            'details' => 'Pay to GTB 0123456789',
        ])->assertRedirect();
        $this->assertDatabaseHas('payment_methods', ['name' => 'Bank Transfer']);

        $pm = PaymentMethod::where('name', 'Bank Transfer')->first();
        $this->actingAs($admin)->delete("/admin/payment-methods/{$pm->id}")->assertRedirect();
        $this->assertDatabaseMissing('payment_methods', ['id' => $pm->id]);
    }

    /** @test */
    public function creating_a_payment_method_requires_name_and_details()
    {
        $this->actingAs($this->admin())->from('/admin/payment-methods/create')
            ->post('/admin/payment-methods', [])
            ->assertSessionHasErrors(['name', 'details']);
    }
}
