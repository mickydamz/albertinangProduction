<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Storefront coupon redemption — the "apply coupon" button on checkout.
 *
 * Route /api/coupons/validate → CouponController@apply → Coupon::validate()
 * + Coupon::calculateDiscount(). This is money-affecting, so the discount
 * maths and every rejection rule are pinned down here. saveCheckout() re-runs
 * the same Coupon::validate()/calculateDiscount(), so these cover that path's
 * pricing rules too.
 */
class CouponRedemptionTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeCoupon(array $overrides = []): Coupon
    {
        return Coupon::create(array_merge([
            'code'          => 'SAVE' . strtoupper(uniqid()),
            'discount_type' => 'percent',
            'value'         => 10,
            'is_active'     => true,
            'used_count'    => 0,
        ], $overrides));
    }

    private function apply(User $user, string $code, float $subtotal): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($user)->postJson('/api/coupons/validate', [
            'code'         => $code,
            'subtotal_ngn' => $subtotal,
        ]);
    }

    // ── Happy paths / discount maths ──────────────────────────────────────────

    /** @test */
    public function a_valid_percent_coupon_applies_the_correct_discount()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['discount_type' => 'percent', 'value' => 10]);

        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(200);
        $res->assertJsonPath('success', true);
        $this->assertEquals(5000, $res->json('discount_ngn')); // 10% of 50,000
    }

    /** @test */
    public function a_percent_coupon_is_capped_at_max_discount_amount()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon([
            'discount_type'       => 'percent',
            'value'               => 50,
            'max_discount_amount' => 3000,
        ]);

        // 50% of 50,000 = 25,000, but the cap holds it at 3,000.
        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(200);
        $this->assertEquals(3000, $res->json('discount_ngn'));
    }

    /** @test */
    public function a_fixed_coupon_never_discounts_more_than_the_subtotal()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['discount_type' => 'fixed', 'value' => 8000]);

        // Fixed ₦8,000 off a ₦5,000 cart must clamp to ₦5,000 — never negative.
        $res = $this->apply($user, $coupon->code, 5000);

        $res->assertStatus(200);
        $this->assertEquals(5000, $res->json('discount_ngn'));
    }

    /** @test */
    public function the_coupon_code_is_matched_case_insensitively()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['code' => 'WELCOME10', 'discount_type' => 'percent', 'value' => 10]);

        $res = $this->apply($user, 'welcome10', 10000);

        $res->assertStatus(200)->assertJsonPath('success', true);
    }

    // ── Rejections ────────────────────────────────────────────────────────────

    /** @test */
    public function an_unknown_code_is_rejected()
    {
        $user = User::factory()->create();

        $res = $this->apply($user, 'DOESNOTEXIST', 50000);

        $res->assertStatus(422)->assertJsonPath('success', false);
        $this->assertStringContainsStringIgnoringCase('invalid', $res->json('message'));
    }

    /** @test */
    public function an_inactive_coupon_is_rejected()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['is_active' => false]);

        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(422)->assertJsonPath('success', false);
        $this->assertStringContainsStringIgnoringCase('active', $res->json('message'));
    }

    /** @test */
    public function an_expired_coupon_is_rejected()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['expires_at' => now()->subDay()]);

        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(422)->assertJsonPath('success', false);
        $this->assertStringContainsStringIgnoringCase('expired', $res->json('message'));
    }

    /** @test */
    public function a_coupon_at_its_usage_limit_is_rejected()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['max_uses' => 5, 'used_count' => 5]);

        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(422)->assertJsonPath('success', false);
        $this->assertStringContainsStringIgnoringCase('usage limit', $res->json('message'));
    }

    /** @test */
    public function a_coupon_below_its_minimum_order_amount_is_rejected()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['min_order_amount' => 20000]);

        $res = $this->apply($user, $coupon->code, 15000);

        $res->assertStatus(422)->assertJsonPath('success', false);
        $this->assertStringContainsStringIgnoringCase('at least', $res->json('message'));
    }

    /** @test */
    public function a_coupon_already_used_by_the_same_user_is_rejected()
    {
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon();

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id'   => $user->id,
            'order_id'  => null,
        ]);

        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(422)->assertJsonPath('success', false);
        $this->assertStringContainsStringIgnoringCase('already used', $res->json('message'));
    }

    /** @test */
    public function a_coupon_used_by_another_user_is_still_valid_for_this_user()
    {
        // The per-user limit is scoped to the caller — someone else's usage
        // must not lock a first-time user out.
        $someoneElse = User::factory()->create();
        $user        = User::factory()->create();
        $coupon      = $this->makeCoupon(['discount_type' => 'percent', 'value' => 10]);

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id'   => $someoneElse->id,
            'order_id'  => null,
        ]);

        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(200)->assertJsonPath('success', true);
        $this->assertEquals(5000, $res->json('discount_ngn'));
    }

    /** @test */
    public function a_reusable_coupon_lets_the_same_customer_redeem_again()
    {
        // multi_use = true is the control that allows a customer to reuse a coupon.
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['multi_use' => true]);

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id'   => $user->id,
            'order_id'  => null,
        ]);

        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(200)->assertJsonPath('success', true);
    }

    /** @test */
    public function an_unlimited_total_coupon_still_blocks_reuse_when_not_marked_reusable()
    {
        // Total redemption limit (max_uses) and per-customer reuse (multi_use) are
        // independent: leaving the total unlimited does NOT let one customer reuse it.
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['max_uses' => null, 'multi_use' => false]);

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id'   => $user->id,
            'order_id'  => null,
        ]);

        $res = $this->apply($user, $coupon->code, 50000);

        $res->assertStatus(422)->assertJsonPath('success', false);
        $this->assertStringContainsStringIgnoringCase('already used', $res->json('message'));
    }

    /** @test */
    public function a_coupon_exactly_at_its_minimum_order_amount_is_accepted()
    {
        // Boundary: subtotal == minimum must pass (the rejection is strictly "below").
        $user   = User::factory()->create();
        $coupon = $this->makeCoupon(['min_order_amount' => 20000]);

        $res = $this->apply($user, $coupon->code, 20000);

        $res->assertStatus(200)->assertJsonPath('success', true);
    }
}
