<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin catalog/content CRUD: categories (AdminCategoryController),
 * brands (AdminBrandController) and FAQs (AdminFaqController).
 */
class AdminContentCrudTest extends TestCase
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

    // ── Categories ──────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_update_and_delete_a_category()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Kitchen'])
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Kitchen']);

        $category = Category::where('name', 'Kitchen')->first();

        $this->actingAs($admin)->put("/admin/categories/{$category->id}", ['name' => 'Kitchenware'])
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Kitchenware']);

        $this->actingAs($admin)->delete("/admin/categories/{$category->id}")
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function creating_a_category_requires_a_name()
    {
        $this->actingAs($this->admin())->from('/admin/categories/create')
            ->post('/admin/categories', [])
            ->assertSessionHasErrors('name');
    }

    // ── Brands ──────────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_a_brand_and_the_slug_is_generated()
    {
        $this->actingAs($this->admin())->post('/admin/brands', ['name' => 'Hisense'])
            ->assertRedirect(route('admin.brands.index'));

        $this->assertDatabaseHas('brands', ['name' => 'Hisense', 'slug' => 'hisense']);
    }

    /** @test */
    public function brand_names_must_be_unique()
    {
        Brand::create(['name' => 'LG', 'slug' => 'lg', 'is_active' => true]);

        $this->actingAs($this->admin())->from('/admin/brands/create')
            ->post('/admin/brands', ['name' => 'LG'])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function an_admin_can_delete_a_brand()
    {
        $brand = Brand::create(['name' => 'Sony', 'slug' => 'sony', 'is_active' => true]);

        $this->actingAs($this->admin())->delete("/admin/brands/{$brand->id}")
            ->assertRedirect(route('admin.brands.index'));

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    // ── FAQs ────────────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_update_and_delete_a_faq()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/faqs', [
            'question' => 'Do you deliver nationwide?',
            'answer'   => 'Yes, across Nigeria.',
            'category' => 'Delivery',
        ])->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', ['question' => 'Do you deliver nationwide?']);

        $faq = Faq::where('question', 'Do you deliver nationwide?')->first();

        $this->actingAs($admin)->put("/admin/faqs/{$faq->id}", [
            'question' => 'Do you deliver nationwide?',
            'answer'   => 'Yes — 2 to 5 working days.',
            'category' => 'Delivery',
        ])->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', ['id' => $faq->id, 'answer' => 'Yes — 2 to 5 working days.']);

        $this->actingAs($admin)->delete("/admin/faqs/{$faq->id}")->assertRedirect();
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }

    /** @test */
    public function creating_a_faq_requires_question_answer_and_category()
    {
        $this->actingAs($this->admin())->from('/admin/faqs/create')
            ->post('/admin/faqs', [])
            ->assertSessionHasErrors(['question', 'answer', 'category']);
    }
}
