<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminMarkupTest extends TestCase
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
    public function admin_can_set_category_markup()
    {
        $cat = $this->category();

        $this->actingAs($this->admin())
            ->post(route('admin.markup.category.update', $cat), ['markup_percent' => 25])
            ->assertRedirect();

        $this->assertEquals(25, $cat->fresh()->markup_percent);
    }

    /** @test */
    public function admin_can_set_product_markup()
    {
        $cat     = $this->category();
        $product = Product::create(['name' => 'Fan', 'price' => 10000, 'category_id' => $cat->id, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.markup.product.update', $product), ['markup_percent' => 12.5])
            ->assertRedirect();

        $this->assertEquals(12.5, $product->fresh()->markup_percent);
    }

    /** @test */
    public function markup_must_be_within_range()
    {
        $cat = $this->category();

        $this->actingAs($this->admin())
            ->post(route('admin.markup.category.update', $cat), ['markup_percent' => 1500])
            ->assertSessionHasErrors('markup_percent');

        $this->actingAs($this->admin())
            ->post(route('admin.markup.category.update', $cat), ['markup_percent' => -5])
            ->assertSessionHasErrors('markup_percent');
    }

    /** @test */
    public function clearing_category_markup_sets_it_null()
    {
        $cat = $this->category();
        $cat->update(['markup_percent' => 20]);

        $this->actingAs($this->admin())
            ->delete(route('admin.markup.category.clear', $cat))
            ->assertRedirect();

        $this->assertNull($cat->fresh()->markup_percent);
    }

    /** @test */
    public function bulk_markup_sets_all_categories_and_clears_lower_overrides()
    {
        $cat  = $this->category('A');
        $sub  = Subcategory::create(['name' => 'Sub ' . uniqid(), 'category_id' => $cat->id, 'markup_percent' => 30]);
        $prod = Product::create(['name' => 'P', 'price' => 5000, 'category_id' => $cat->id, 'markup_percent' => 40, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.markup.bulk'), ['markup_percent' => 10])
            ->assertRedirect();

        // Every category gets the global markup; product/subcategory overrides are wiped.
        $this->assertEquals(10, $cat->fresh()->markup_percent);
        $this->assertNull($sub->fresh()->markup_percent);
        $this->assertNull($prod->fresh()->markup_percent);
    }

    /** @test */
    public function a_non_admin_cannot_set_markup()
    {
        $cat = $this->category();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.markup.category.update', $cat), ['markup_percent' => 25])
            ->assertForbidden();

        $this->assertNull($cat->fresh()->markup_percent);
    }
}
