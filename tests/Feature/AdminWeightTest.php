<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminWeightTest extends TestCase
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

    // ── Global thresholds (weight + order value) ────────────────────────────────

    /** @test */
    public function admin_can_update_the_global_truck_thresholds()
    {
        $this->actingAs($this->admin())
            ->post(route('admin.weight.thresholds.update'), [
                'truck_weight_threshold_kg'       => 45,
                'truck_order_value_threshold_ngn' => 750000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'truck_weight_threshold_kg', 'value' => '45']);
        $this->assertDatabaseHas('settings', ['key' => 'truck_order_value_threshold_ngn', 'value' => '750000']);
    }

    /** @test */
    public function thresholds_are_validated()
    {
        // weight must be >= 1, order value >= 0, both required
        $this->actingAs($this->admin())
            ->post(route('admin.weight.thresholds.update'), [
                'truck_weight_threshold_kg'       => 0,
                'truck_order_value_threshold_ngn' => -100,
            ])
            ->assertSessionHasErrors(['truck_weight_threshold_kg', 'truck_order_value_threshold_ngn']);
    }

    /** @test */
    public function threshold_defaults_apply_when_unset()
    {
        // With no rows set, the controller falls back to the documented defaults.
        $this->assertSame(30.0, (float) Setting::get('truck_weight_threshold_kg', 30));
        $this->assertSame(1000000.0, (float) Setting::get('truck_order_value_threshold_ngn', 1000000));
    }

    // ── Per-entity weights ──────────────────────────────────────────────────────

    /** @test */
    public function admin_can_set_and_clear_a_product_weight()
    {
        $cat     = Category::create(['name' => 'Cat ' . uniqid()]);
        $product = Product::create(['name' => 'Freezer', 'price' => 200000, 'category_id' => $cat->id, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.weight.product.update', $product), ['weight_kg' => 42.5])
            ->assertRedirect();
        $this->assertEquals(42.5, $product->fresh()->weight_kg);

        $this->actingAs($this->admin())
            ->delete(route('admin.weight.product.clear', $product))
            ->assertRedirect();
        $this->assertNull($product->fresh()->weight_kg);
    }

    /** @test */
    public function admin_can_set_a_category_weight()
    {
        $cat = Category::create(['name' => 'Cat ' . uniqid()]);

        $this->actingAs($this->admin())
            ->post(route('admin.weight.category.update', $cat), ['estimated_weight_kg' => 15])
            ->assertRedirect();

        $this->assertEquals(15, $cat->fresh()->estimated_weight_kg);
    }

    /** @test */
    public function product_weight_is_validated()
    {
        $cat     = Category::create(['name' => 'Cat ' . uniqid()]);
        $product = Product::create(['name' => 'X', 'price' => 1000, 'category_id' => $cat->id, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.weight.product.update', $product), ['weight_kg' => -1])
            ->assertSessionHasErrors('weight_kg');
    }

    /** @test */
    public function a_non_admin_cannot_update_thresholds()
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.weight.thresholds.update'), [
                'truck_weight_threshold_kg'       => 45,
                'truck_order_value_threshold_ngn' => 750000,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('settings', ['key' => 'truck_weight_threshold_kg', 'value' => '45']);
    }
}
