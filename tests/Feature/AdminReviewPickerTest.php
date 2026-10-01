<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Admin "create / edit review" screens (AdminReviewController).
 *
 * The forms must let an admin SEARCH for both the product and the user (rather
 * than scroll a giant <select>), and the product picker must surface a picture
 * so it's obvious which item is being reviewed.
 *
 * We assert against the rendered HTML: the search inputs exist, and each
 * product's image URL is embedded in the picker's JSON payload.
 */
class AdminReviewPickerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        return $user;
    }

    private function productWithImage(string $name, string $imageUrl): Product
    {
        $product = Product::create([
            'name'      => $name,
            'price'     => 50000,
            'stock'     => 10,
            'is_active' => true,
        ]);
        Image::create(['product_id' => $product->id, 'image_url' => $imageUrl]);
        return $product;
    }

    // ── Create page ───────────────────────────────────────────────────────────

    /** @test */
    public function create_page_exposes_searchable_product_and_user_pickers_with_images()
    {
        $product = $this->productWithImage('Samsung QLED TV', 'products/qled.jpg');
        User::factory()->create(['name' => 'Jane Buyer']); // note: default role, still selectable list
        $reviewer = User::factory()->create();
        $reviewer->role = 'user';
        $reviewer->save();

        $res = $this->actingAs($this->admin())->get('/admin/reviews/create');

        $res->assertOk();
        // Both pickers are search inputs, not plain dropdowns.
        $res->assertSee('id="product-search"', false);
        $res->assertSee('id="user-search"', false);
        $res->assertSee('type="hidden" name="product_id"', false);
        $res->assertSee('type="hidden" name="user_id"', false);
        // The product's picture is wired into the picker payload. (@json escapes
        // slashes to \/, so match on the slash-free filename.)
        $res->assertSee('qled.jpg', false);
        // Reviewer (role=user) is available to pick.
        $res->assertSee($reviewer->email, false);
    }

    // ── Edit page ──────────────────────────────────────────────────────────────

    /** @test */
    public function edit_page_exposes_searchable_pickers_and_preselects_current_values()
    {
        $product  = $this->productWithImage('LG Washing Machine', 'products/lg-wm.jpg');
        $reviewer = User::factory()->create();
        $reviewer->role = 'user';
        $reviewer->save();

        $review = Review::create([
            'product_id' => $product->id,
            'user_id'    => $reviewer->id,
            'content'    => 'Great product',
            'rating'     => 5,
        ]);

        $res = $this->actingAs($this->admin())->get("/admin/reviews/{$review->id}/edit");

        $res->assertOk();
        $res->assertSee('id="product-search"', false);
        $res->assertSee('id="user-search"', false);
        // Product image is available to the picker. (@json escapes slashes.)
        $res->assertSee('lg-wm.jpg', false);
        // The current selection is pre-filled via the JS bootstrap.
        $res->assertSee("currentProductId = {$product->id}", false);
        $res->assertSee("currentUserId    = {$reviewer->id}", false);
    }

    // ── Persistence: the whole point — reviews must actually save ─────────────

    /** @test */
    public function admin_can_store_a_review_using_the_content_column()
    {
        $product  = $this->productWithImage('Panasonic Microwave', 'products/pana.jpg');
        $reviewer = User::factory()->create();

        $res = $this->actingAs($this->admin())->post('/admin/reviews', [
            'product_id' => $product->id,
            'user_id'    => $reviewer->id,
            'content'    => 'Solid build, works great.',
            'rating'     => 4,
        ]);

        $res->assertRedirect(route('admin.reviews.index'));
        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'user_id'    => $reviewer->id,
            'content'    => 'Solid build, works great.',
            'rating'     => 4,
        ]);
    }

    /** @test */
    public function admin_can_update_a_review_content()
    {
        $product  = $this->productWithImage('Hisense TV', 'products/hisense.jpg');
        $reviewer = User::factory()->create();
        $review = Review::create([
            'product_id' => $product->id,
            'user_id'    => $reviewer->id,
            'content'    => 'Original text',
            'rating'     => 3,
        ]);

        $res = $this->actingAs($this->admin())->put("/admin/reviews/{$review->id}", [
            'product_id' => $product->id,
            'user_id'    => $reviewer->id,
            'content'    => 'Edited text',
            'rating'     => 5,
        ]);

        $res->assertRedirect(route('admin.reviews.index'));
        $this->assertDatabaseHas('reviews', [
            'id'      => $review->id,
            'content' => 'Edited text',
            'rating'  => 5,
        ]);
    }

    /** @test */
    public function product_page_renders_the_review_content_and_author_name()
    {
        $product  = $this->productWithImage('Bosch Dishwasher', 'products/bosch.jpg');
        $reviewer = User::factory()->create(['name' => 'Ada Reviewer']);
        Review::create([
            'product_id' => $product->id,
            'user_id'    => $reviewer->id,
            'content'    => 'Whisper quiet and efficient.',
            'rating'     => 5,
        ]);

        $res = $this->get("/product/{$product->id}");

        $res->assertOk();
        $res->assertSee('Whisper quiet and efficient.', false); // content column renders
        $res->assertSee('Ada Reviewer', false);                 // user_name accessor via author
    }
}
