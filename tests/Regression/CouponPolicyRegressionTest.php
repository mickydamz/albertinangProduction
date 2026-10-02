<?php
namespace Tests\Regression;
use App\Models\{Coupon, CouponUsage};

/** Real source rules: global usage and per-customer reuse are independent. */
class CouponPolicyRegressionTest extends RegressionTestCase
{
    private function coupon(array $overrides = []): Coupon
    {
        return Coupon::create(array_merge([
            'code' => 'REGRESSION-SAVE10', 'discount_type' => 'percent', 'value' => 10,
            'max_discount_amount' => 5000, 'min_order_amount' => 5000,
            'max_uses' => null, 'multi_use' => true, 'used_count' => 1, 'is_active' => true,
        ], $overrides));
    }
    public function test_unlimited_repeat_use_accepts_the_same_customer_above_one_million(): void
    {
        $user = $this->customer(); $coupon = $this->coupon();
        CouponUsage::create(['coupon_id' => $coupon->id, 'user_id' => $user->id, 'order_id' => null]);
        foreach (range(1, 2) as $attempt) {
            $this->actingAs($user)->postJson('/api/coupons/validate', ['code' => $coupon->code, 'subtotal_ngn' => 1512000])
                ->assertOk()->assertJsonPath('success', true)->assertJsonPath('discount_ngn', 5000);
        }
        $this->assertEquals(1, $coupon->fresh()->used_count, 'Validation alone must not redeem the coupon.');
        $this->assertDatabaseCount('coupon_usages', 1);
    }
    public function test_unlimited_global_usage_does_not_override_single_use_per_customer(): void
    {
        $user = $this->customer(); $coupon = $this->coupon(['multi_use' => false]);
        CouponUsage::create(['coupon_id' => $coupon->id, 'user_id' => $user->id, 'order_id' => null]);
        $this->actingAs($user)->postJson('/api/coupons/validate', ['code' => $coupon->code, 'subtotal_ngn' => 1512000])
            ->assertStatus(422)->assertJsonPath('message', 'You have already used this coupon.');
    }
    public function test_repeat_use_does_not_override_an_exhausted_global_limit(): void
    {
        $user = $this->customer(); $coupon = $this->coupon(['max_uses' => 1]);
        $this->actingAs($user)->postJson('/api/coupons/validate', ['code' => $coupon->code, 'subtotal_ngn' => 1512000])
            ->assertStatus(422)->assertJsonPath('message', 'This coupon has reached its usage limit.');
    }
    public function test_high_value_discount_keeps_payable_amount_above_one_million(): void
    {
        $coupon = $this->coupon();
        $discount = $coupon->calculateDiscount(1512000);
        $this->assertEquals(5000, $discount);
        $this->assertEquals(1507000, 1512000 - $discount);
    }
}
