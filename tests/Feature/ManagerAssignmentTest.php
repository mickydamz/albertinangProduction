<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Manager ⇄ product assignment.
 *
 * Products are assigned to a manager through their BRAND: a Brand carries a
 * manager_id, the Product model's saving() hook copies it onto every product of
 * that brand, and the admin "assign brand" screen can hand a whole brand (a
 * group/"category" of products) to a manager and backdate the existing stock.
 *
 * A manager then only ever sees and edits the products that belong to them.
 *
 * These tests cover all three layers:
 *   1. auto-assignment on save (Product::booted)
 *   2. bulk brand assignment (BrandAssignmentController@assign)
 *   3. per-manager scoping (ManagerProductController)
 */
class ManagerAssignmentTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function manager(): User
    {
        $u = User::factory()->create();
        $u->role = 'manager';
        $u->save();
        return $u;
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->role = 'admin';
        $u->save();
        return $u;
    }

    private function product(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name'      => 'Product ' . uniqid(),
            'price'     => 50000,
            'stock'     => 10,
            'is_active' => true,
        ], $attrs));
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  1. Auto-assignment via the Product saving() hook
    // ═══════════════════════════════════════════════════════════════════════

    /** @test */
    public function a_product_created_with_a_managed_brand_string_inherits_the_manager()
    {
        $manager = $this->manager();
        Brand::create(['name' => 'Samsung', 'slug' => 'samsung', 'manager_id' => $manager->id, 'is_active' => true]);

        $product = $this->product(['brand' => 'Samsung']);

        $this->assertSame($manager->id, $product->fresh()->manager_id);
    }

    /** @test */
    public function a_product_created_with_a_managed_brand_id_inherits_the_manager()
    {
        $manager = $this->manager();
        $brand = Brand::create(['name' => 'LG', 'slug' => 'lg', 'manager_id' => $manager->id, 'is_active' => true]);

        $product = $this->product(['brand_id' => $brand->id, 'brand' => 'LG']);

        $this->assertSame($manager->id, $product->fresh()->manager_id);
    }

    /** @test */
    public function an_explicit_manager_is_not_overridden_by_the_brand()
    {
        $brandManager   = $this->manager();
        $chosenManager  = $this->manager();
        Brand::create(['name' => 'Hisense', 'slug' => 'hisense', 'manager_id' => $brandManager->id, 'is_active' => true]);

        // Manager explicitly set on creation must win over the brand's manager.
        $product = $this->product(['brand' => 'Hisense', 'manager_id' => $chosenManager->id]);

        $this->assertSame($chosenManager->id, $product->fresh()->manager_id);
    }

    /** @test */
    public function a_product_whose_brand_has_no_manager_stays_unassigned()
    {
        Brand::create(['name' => 'Nexus', 'slug' => 'nexus', 'manager_id' => null, 'is_active' => true]);

        $product = $this->product(['brand' => 'Nexus']);

        $this->assertNull($product->fresh()->manager_id);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  2. Bulk assignment: hand a whole brand (group of products) to a manager
    // ═══════════════════════════════════════════════════════════════════════

    /** @test */
    public function assigning_a_brand_sets_the_brand_manager_and_backdates_existing_products()
    {
        $manager = $this->manager();
        $brand   = Brand::create(['name' => 'Bosch', 'slug' => 'bosch', 'is_active' => true]);

        // Products already exist under the brand with no manager yet.
        $p1 = $this->product(['brand' => 'Bosch', 'brand_id' => $brand->id]);
        $p2 = $this->product(['brand' => 'Bosch', 'brand_id' => $brand->id]);

        $res = $this->actingAs($this->admin())->post('/admin/brands/assign', [
            'brand'      => 'Bosch',
            'manager_id' => $manager->id,
        ]);

        $res->assertRedirect();
        $this->assertSame($manager->id, $brand->fresh()->manager_id);
        $this->assertSame($manager->id, $p1->fresh()->manager_id);
        $this->assertSame($manager->id, $p2->fresh()->manager_id);
    }

    /** @test */
    public function assigning_a_legacy_string_only_brand_links_products_to_a_brand_record_and_manager()
    {
        $manager = $this->manager();

        // Legacy product: brand stored as a plain string, no brand_id, no Brand row.
        $legacy = $this->product(['brand' => 'Panasonic', 'brand_id' => null]);
        $this->assertNull($legacy->fresh()->brand_id);

        $this->actingAs($this->admin())->post('/admin/brands/assign', [
            'brand'      => 'Panasonic',
            'manager_id' => $manager->id,
        ])->assertRedirect();

        // A Brand record is created, the product is linked to it, and gets the manager.
        $brand = Brand::where('name', 'Panasonic')->first();
        $this->assertNotNull($brand);
        $this->assertSame($manager->id, $brand->manager_id);

        $legacy->refresh();
        $this->assertSame($brand->id, $legacy->brand_id);
        $this->assertSame($manager->id, $legacy->manager_id);
    }

    /** @test */
    public function reassigning_a_brand_moves_all_its_products_to_the_new_manager()
    {
        $first  = $this->manager();
        $second = $this->manager();
        $brand  = Brand::create(['name' => 'Sony', 'slug' => 'sony', 'manager_id' => $first->id, 'is_active' => true]);
        $product = $this->product(['brand' => 'Sony', 'brand_id' => $brand->id]);
        $this->assertSame($first->id, $product->fresh()->manager_id);

        $this->actingAs($this->admin())->post('/admin/brands/assign', [
            'brand'      => 'Sony',
            'manager_id' => $second->id,
        ])->assertRedirect();

        $this->assertSame($second->id, $brand->fresh()->manager_id);
        $this->assertSame($second->id, $product->fresh()->manager_id);
    }

    /** @test */
    public function unassigning_a_brand_clears_the_manager_from_its_products()
    {
        $manager = $this->manager();
        $brand   = Brand::create(['name' => 'Midea', 'slug' => 'midea', 'manager_id' => $manager->id, 'is_active' => true]);
        $product = $this->product(['brand' => 'Midea', 'brand_id' => $brand->id]);
        $this->assertSame($manager->id, $product->fresh()->manager_id);

        $this->actingAs($this->admin())->post('/admin/brands/assign', [
            'brand'      => 'Midea',
            'manager_id' => '', // unassign
        ])->assertRedirect();

        $this->assertNull($brand->fresh()->manager_id);
        $this->assertNull($product->fresh()->manager_id);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  3. Per-manager scoping (ManagerProductController)
    // ═══════════════════════════════════════════════════════════════════════

    /** @test */
    public function a_manager_only_sees_their_own_products()
    {
        $mine   = $this->manager();
        $theirs = $this->manager();

        $myProduct    = $this->product(['manager_id' => $mine->id]);
        $otherProduct = $this->product(['manager_id' => $theirs->id]);

        $res = $this->actingAs($mine)->get('/manager/products');

        $res->assertOk();
        $ids = $res->viewData('products')->pluck('id')->all();
        $this->assertContains($myProduct->id, $ids);
        $this->assertNotContains($otherProduct->id, $ids);
    }

    /** @test */
    public function a_manager_can_update_their_own_product()
    {
        $manager = $this->manager();
        $product = $this->product(['manager_id' => $manager->id, 'name' => 'Old Name']);

        $res = $this->actingAs($manager)->put("/manager/products/{$product->id}", [
            'name'  => 'New Name',
            'price' => 60000,
            'stock' => 7,
        ]);

        $res->assertRedirect(route('manager.products.index'));
        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'New Name',
            'price' => 60000,
            'stock' => 7,
        ]);
    }

    /** @test */
    public function a_manager_cannot_update_another_managers_product()
    {
        $manager = $this->manager();
        $other   = $this->manager();
        $foreign = $this->product(['manager_id' => $other->id, 'name' => 'Not Mine']);

        $res = $this->actingAs($manager)->put("/manager/products/{$foreign->id}", [
            'name'  => 'Hijacked',
            'price' => 1,
            'stock' => 0,
        ]);

        $res->assertNotFound();
        $this->assertDatabaseHas('products', ['id' => $foreign->id, 'name' => 'Not Mine']);
    }

    /** @test */
    public function a_manager_can_toggle_their_own_product_but_not_another_managers()
    {
        $manager = $this->manager();
        $other   = $this->manager();
        $mine    = $this->product(['manager_id' => $manager->id, 'is_active' => true]);
        $foreign = $this->product(['manager_id' => $other->id, 'is_active' => true]);

        // Own product toggles.
        $this->actingAs($manager)
            ->patch("/manager/products/{$mine->id}/toggle-active")
            ->assertOk()
            ->assertJson(['success' => true, 'is_active' => false]);

        // Another manager's product is invisible → 404.
        $this->actingAs($manager)
            ->patchJson("/manager/products/{$foreign->id}/toggle-active")
            ->assertNotFound();
    }

    /** @test */
    public function a_non_manager_cannot_reach_the_manager_area()
    {
        $user = User::factory()->create(); // default role (not manager/admin)

        $this->actingAs($user)->getJson('/manager/products')->assertStatus(403);
    }
}
