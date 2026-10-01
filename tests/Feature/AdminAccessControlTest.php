<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * The admin area is gated by the `role:admin` middleware (CheckRole):
 * guests are sent to login, logged-in non-admins get 403, admins get in.
 */
class AdminAccessControlTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** @test */
    public function a_guest_is_redirected_to_login_from_the_admin_area()
    {
        $this->get('/admin/users')->assertRedirect(route('login'));
    }

    /** @test */
    public function a_regular_user_is_forbidden_from_the_admin_area()
    {
        $this->actingAs(User::factory()->create()) // default (non-admin) role
            ->get('/admin/users')->assertForbidden();
    }

    /** @test */
    public function a_non_admin_cannot_perform_admin_writes()
    {
        // The guard runs before the controller, so a POST is blocked outright.
        $this->actingAs(User::factory()->create())
            ->post('/admin/categories', ['name' => 'Hacked'])
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'Hacked']);
    }

    /** @test */
    public function an_admin_can_reach_the_admin_area()
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/users')->assertOk();
    }

    // ── Toggle-active routes defined outside the admin group ─────────────────────
    // These live in web.php outside the role:admin group, so they carry the guard
    // explicitly. Regression: they previously had NO middleware at all.

    /** @test */
    public function a_guest_cannot_toggle_brand_category_or_subcategory_visibility()
    {
        $brand       = Brand::create(['name' => 'B ' . uniqid(), 'is_active' => true]);
        $category    = Category::create(['name' => 'C ' . uniqid(), 'is_active' => true]);
        $subcategory = Subcategory::create(['name' => 'S ' . uniqid(), 'category_id' => $category->id, 'is_active' => true]);

        $this->patch(route('admin.brands.toggleActive', $brand))->assertRedirect(route('login'));
        $this->patch(route('admin.categories.toggleActive', $category))->assertRedirect(route('login'));
        $this->patch(route('admin.subcategories.toggleActive', $subcategory))->assertRedirect(route('login'));

        $this->assertTrue((bool) $brand->fresh()->is_active);
        $this->assertTrue((bool) $category->fresh()->is_active);
        $this->assertTrue((bool) $subcategory->fresh()->is_active);
    }

    /** @test */
    public function a_non_admin_cannot_toggle_brand_category_or_subcategory_visibility()
    {
        $user        = User::factory()->create(); // non-admin
        $brand       = Brand::create(['name' => 'B ' . uniqid(), 'is_active' => true]);
        $category    = Category::create(['name' => 'C ' . uniqid(), 'is_active' => true]);
        $subcategory = Subcategory::create(['name' => 'S ' . uniqid(), 'category_id' => $category->id, 'is_active' => true]);

        $this->actingAs($user)->patch(route('admin.brands.toggleActive', $brand))->assertForbidden();
        $this->actingAs($user)->patch(route('admin.categories.toggleActive', $category))->assertForbidden();
        $this->actingAs($user)->patch(route('admin.subcategories.toggleActive', $subcategory))->assertForbidden();

        $this->assertTrue((bool) $brand->fresh()->is_active);
        $this->assertTrue((bool) $category->fresh()->is_active);
        $this->assertTrue((bool) $subcategory->fresh()->is_active);
    }
}
