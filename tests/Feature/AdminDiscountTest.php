<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminDiscountTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->role = 'admin';
        $u->save();
        return $u;
    }

    private function category(string $name = 'Cat'): Category
    {
        return Category::create(['name' => $name . ' ' . uniqid()]);
    }

    /** @test */
    public function admin_can_set_a_product_discount()
    {
        $cat     = $this->category();
        $product = Product::create(['name' => 'TV', 'price' => 90000, 'category_id' => $cat->id, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.discount.product.update', $product), ['discount_percent' => 15])
            ->assertRedirect();

        $this->assertEquals(15, $product->fresh()->discount_percent);
    }

    /** @test */
    public function admin_can_set_a_category_discount_with_expiry()
    {
        $cat    = $this->category();
        $expiry = now()->addDays(7)->format('Y-m-d H:i:s');

        $this->actingAs($this->admin())
            ->post(route('admin.discount.category.update', $cat), [
                'discount_percent'    => 20,
                'discount_expires_at' => $expiry,
            ])
            ->assertRedirect();

        $fresh = $cat->fresh();
        $this->assertEquals(20, $fresh->discount_percent);
        $this->assertNotNull($fresh->discount_expires_at);
    }

    /** @test */
    public function discount_percent_must_be_0_to_100()
    {
        $cat = $this->category();

        $this->actingAs($this->admin())
            ->post(route('admin.discount.category.update', $cat), ['discount_percent' => 150])
            ->assertSessionHasErrors('discount_percent');
    }

    /** @test */
    public function discount_expiry_must_be_in_the_future()
    {
        $cat = $this->category();

        $this->actingAs($this->admin())
            ->post(route('admin.discount.category.update', $cat), [
                'discount_percent'    => 10,
                'discount_expires_at' => now()->subDay()->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors('discount_expires_at');
    }

    /** @test */
    public function bulk_discount_applies_to_all_categories_and_clears_overrides()
    {
        $cat  = $this->category();
        $sub  = Subcategory::create(['name' => 'Sub ' . uniqid(), 'category_id' => $cat->id, 'discount_percent' => 30]);
        $prod = Product::create(['name' => 'P', 'price' => 5000, 'category_id' => $cat->id, 'discount_percent' => 25, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.discount.bulk'), ['discount_percent' => 5])
            ->assertRedirect();

        $this->assertEquals(5, $cat->fresh()->discount_percent);
        $this->assertNull($sub->fresh()->discount_percent);
        $this->assertNull($prod->fresh()->discount_percent);
    }

    /** @test */
    public function clear_all_wipes_every_discount()
    {
        $cat  = $this->category();
        $cat->update(['discount_percent' => 10]);
        $prod = Product::create(['name' => 'P', 'price' => 5000, 'category_id' => $cat->id, 'discount_percent' => 20, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->delete(route('admin.discount.clearAll'))
            ->assertRedirect();

        $this->assertNull($cat->fresh()->discount_percent);
        $this->assertNull($prod->fresh()->discount_percent);
    }

    /** @test */
    public function a_non_admin_cannot_set_a_discount()
    {
        $cat = $this->category();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.discount.category.update', $cat), ['discount_percent' => 10])
            ->assertForbidden();

        $this->assertNull($cat->fresh()->discount_percent);
    }
}
