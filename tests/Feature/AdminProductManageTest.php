<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin product management (AdminProductController store / toggleActive /
 * destroy / bulkMarkup).
 */
class AdminProductManageTest extends TestCase
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

    private function makeProduct(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name'      => 'Product ' . uniqid(),
            'price'     => 50000,
            'stock'     => 10,
            'is_active' => true,
        ], $attrs));
    }

    /** @test */
    public function an_admin_can_create_a_product()
    {
        $category = Category::create(['name' => 'Appliances', 'is_active' => true]);

        $this->actingAs($this->admin())->post('/admin/products', [
            'name'        => 'New Blender',
            'price'       => 45000,
            'rating'      => 4,
            'stock'       => 12,
            'category_id' => $category->id,
            'is_active'   => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('products', ['name' => 'New Blender', 'category_id' => $category->id]);
    }

    /** @test */
    public function creating_a_product_requires_name_price_and_category()
    {
        $this->actingAs($this->admin())->from('/admin/products/create')->post('/admin/products', [
            'rating' => 4,
        ])->assertSessionHasErrors(['name', 'price', 'stock', 'category_id']);
    }

    /** @test */
    public function an_admin_can_toggle_a_products_active_state()
    {
        $product = $this->makeProduct(['is_active' => true]);

        $this->actingAs($this->admin())
            ->patch("/admin/products/{$product->id}/toggle-active")
            ->assertRedirect();

        $this->assertFalse((bool) $product->fresh()->is_active);
    }

    /** @test */
    public function an_admin_can_delete_a_product()
    {
        $product = $this->makeProduct();

        $this->actingAs($this->admin())->delete("/admin/products/{$product->id}")
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function bulk_markup_applies_a_percentage_to_every_product()
    {
        $a = $this->makeProduct();
        $b = $this->makeProduct();

        $this->actingAs($this->admin())->post('/admin/products/bulk-markup', [
            'markup_percent' => 15,
        ])->assertRedirect(route('admin.products.index'));

        $this->assertEquals(15, (float) $a->fresh()->markup_percent);
        $this->assertEquals(15, (float) $b->fresh()->markup_percent);
    }
}
