<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Admin coupon lifecycle (AdminCouponController): create, edit, deactivate and
 * remove — plus the guard that a coupon already applied to orders cannot be
 * deleted (it must be deactivated instead), and that non-admins are locked out.
 */
class AdminCouponManageTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeCoupon(array $attrs = []): Coupon
    {
        return Coupon::create(array_merge([
            'code'          => 'SAVE' . strtoupper(uniqid()),
            'discount_type' => 'fixed',
            'value'         => 2000,
            'is_active'     => true,
            'used_count'    => 0,
        ], $attrs));
    }

    /** @test */
    public function an_admin_can_create_a_coupon()
    {
        $this->actingAs($this->admin())->post(route('admin.coupons.store'), [
            'code'          => 'welcome10',
            'discount_type' => 'percent',
            'value'         => 10,
            'is_active'     => 1,
        ])->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseHas('coupons', ['code' => 'WELCOME10', 'discount_type' => 'percent']);
    }

    /** @test */
    public function creating_a_coupon_validates_required_fields_and_percent_cap()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->from(route('admin.coupons.create'))
            ->post(route('admin.coupons.store'), [])
            ->assertSessionHasErrors(['code', 'discount_type', 'value']);

        $this->actingAs($admin)->from(route('admin.coupons.create'))
            ->post(route('admin.coupons.store'), [
                'code' => 'TOOBIG', 'discount_type' => 'percent', 'value' => 150,
            ])->assertSessionHasErrors('value');
    }

    /** @test */
    public function an_admin_can_edit_a_coupon()
    {
        $coupon = $this->makeCoupon(['value' => 2000]);

        $this->actingAs($this->admin())->put(route('admin.coupons.update', $coupon), [
            'code'          => $coupon->code,
            'discount_type' => 'fixed',
            'value'         => 7500,
        ])->assertRedirect(route('admin.coupons.index'));

        $this->assertSame(7500.0, (float) $coupon->fresh()->value);
    }

    /** @test */
    public function an_admin_can_deactivate_a_coupon()
    {
        $coupon = $this->makeCoupon(['is_active' => true]);

        $this->actingAs($this->admin())
            ->patch(route('admin.coupons.toggleActive', $coupon))
            ->assertRedirect();

        $this->assertFalse((bool) $coupon->fresh()->is_active);
    }

    /** @test */
    public function an_admin_can_delete_an_unused_coupon()
    {
        $coupon = $this->makeCoupon();

        $this->actingAs($this->admin())->delete(route('admin.coupons.destroy', $coupon))
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    /** @test */
    public function a_coupon_already_applied_to_orders_cannot_be_deleted()
    {
        $coupon = $this->makeCoupon();
        Order::create([
            'user_id'        => User::factory()->create()->id,
            'status'         => 'paid',
            'total'          => 48000,
            'payment_method' => 'paystack',
            'coupon_id'      => $coupon->id,
        ]);

        $this->actingAs($this->admin())->delete(route('admin.coupons.destroy', $coupon))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
    }

    /** @test */
    public function a_non_admin_cannot_manage_coupons()
    {
        $coupon = $this->makeCoupon();
        $user   = User::factory()->create(); // non-admin

        $this->actingAs($user)->delete(route('admin.coupons.destroy', $coupon))->assertForbidden();
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
    }
}
