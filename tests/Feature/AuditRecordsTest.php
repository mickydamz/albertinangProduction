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
 * Admin mutations on auditable models (Product) write an audit_logs entry that
 * records the event, the affected record and the acting admin. Covers the
 * create / update / delete lifecycle through the real admin HTTP endpoints.
 */
class AuditRecordsTest extends TestCase
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
    public function creating_a_product_as_admin_records_a_created_audit_entry()
    {
        $admin    = $this->admin();
        $category = Category::create(['name' => 'Appliances', 'is_active' => true]);

        $this->actingAs($admin)->post('/admin/products', [
            'name'        => 'Audited Blender',
            'price'       => 45000,
            'stock'       => 5,
            'category_id' => $category->id,
            'is_active'   => 1,
        ])->assertRedirect();

        $product = Product::where('name', 'Audited Blender')->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'event'          => 'created',
            'auditable_type' => Product::class,
            'auditable_id'   => $product->id,
            'user_id'        => $admin->id,
        ]);
    }

    /** @test */
    public function toggling_a_product_records_an_updated_audit_entry()
    {
        $admin   = $this->admin();
        $product = $this->makeProduct(['is_active' => true]);

        $this->actingAs($admin)->patch("/admin/products/{$product->id}/toggle-active")->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'event'          => 'updated',
            'auditable_type' => Product::class,
            'auditable_id'   => $product->id,
            'user_id'        => $admin->id,
        ]);
    }

    /** @test */
    public function deleting_a_product_records_a_deleted_audit_entry()
    {
        $admin   = $this->admin();
        $product = $this->makeProduct();
        $id      = $product->id;

        $this->actingAs($admin)->delete("/admin/products/{$id}")->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'event'          => 'deleted',
            'auditable_type' => Product::class,
            'auditable_id'   => $id,
            'user_id'        => $admin->id,
        ]);
    }
}
