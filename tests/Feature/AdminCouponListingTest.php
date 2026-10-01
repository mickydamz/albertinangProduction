<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Search + pagination on the admin Coupons index.
 */
class AdminCouponListingTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        return $user;
    }

    private function coupon(string $code): Coupon
    {
        return Coupon::create([
            'code'          => $code,
            'discount_type' => 'percentage',
            'value'         => 10,
            'is_active'     => true,
            'used_count'    => 0,
        ]);
    }

    /** @test */
    public function coupons_index_renders_and_search_filters_by_code()
    {
        $keep = 'SAVE' . strtoupper(substr(uniqid(), -5));
        $drop = 'OFF' . strtoupper(substr(uniqid(), -5));
        $this->coupon($keep);
        $this->coupon($drop);

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.coupons.index'))->assertOk();

        $this->actingAs($admin)->get(route('admin.coupons.index', ['search' => $keep]))
            ->assertOk()
            ->assertSee($keep)
            ->assertDontSee($drop);
    }

    /** @test */
    public function coupons_index_paginates_at_20_per_page()
    {
        $tag = 'PG' . strtoupper(substr(uniqid(), -5));
        // 21 matching coupons -> at 20/page there must be a second page.
        for ($i = 1; $i <= 21; $i++) {
            $this->coupon($tag . str_pad((string) $i, 2, '0', STR_PAD_LEFT));
        }

        $admin = $this->admin();

        // Page 1 renders and offers a link to page 2 (proves it paginated).
        $this->actingAs($admin)->get(route('admin.coupons.index', ['search' => $tag]))
            ->assertOk()
            ->assertSee('page=2', false);

        // Page 2 renders too.
        $this->actingAs($admin)->get(route('admin.coupons.index', ['search' => $tag, 'page' => 2]))
            ->assertOk();
    }
}
