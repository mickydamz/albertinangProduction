<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin "show" pages for products and users
 * (AdminProductController@show, AdminUserController@show).
 */
class AdminShowPagesTest extends TestCase
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

    private function makeProduct(): Product
    {
        return Product::create([
            'name'      => 'Product ' . uniqid(),
            'price'     => 50000,
            'stock'     => 10,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function an_admin_can_view_a_product_show_page()
    {
        $product = $this->makeProduct();

        $res = $this->actingAs($this->admin())->get("/admin/products/{$product->id}");

        $res->assertOk();
        $res->assertViewIs('admin.products.show');
        $this->assertSame($product->id, $res->viewData('product')->id);
    }

    /** @test */
    public function an_admin_can_view_a_user_show_page()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($this->admin())->get("/admin/users/{$user->id}");

        $res->assertOk();
        $res->assertViewIs('admin.users.show');
        $this->assertSame($user->id, $res->viewData('user')->id);
    }

    /** @test */
    public function a_non_admin_is_forbidden_from_the_show_pages()
    {
        $product = $this->makeProduct();
        $user    = User::factory()->create(); // default (non-admin) role

        $this->actingAs($user)->get("/admin/products/{$product->id}")->assertForbidden();
        $this->actingAs($user)->get("/admin/users/{$user->id}")->assertForbidden();
    }

    /** @test */
    public function a_missing_record_returns_404()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/products/99999999')->assertNotFound();
        $this->actingAs($admin)->get('/admin/users/99999999')->assertNotFound();
    }
}
