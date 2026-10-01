<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * User-facing SEARCH page (ProductController@searchProducts, route search) and
 * the AJAX suggestions endpoint (getSearchSuggestions, route search.suggestions).
 *
 * The search must:
 *   • match on name, brand, category and subcategory names (and tags/description
 *     once the term is long enough),
 *   • rank name-prefix hits above the rest (relevance order),
 *   • treat an empty / "*" term as "browse everything",
 *   • reject a 1-character term with a friendly error and no DB hit,
 *   • only ever return ACTIVE products,
 *   • honour the same brand / price / availability filters as the category page.
 *
 * Assertions run against the view data (products paginator) rather than markup.
 */
class SearchPageTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeProduct(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name'      => 'Product ' . uniqid(),
            'price'     => 50000,
            'stock'     => 10,
            'is_active' => true,
        ], $attrs));
    }

    private function shownIds(\Illuminate\Testing\TestResponse $res): array
    {
        return $res->viewData('products')->pluck('id')->all();
    }

    // ── Text matching ─────────────────────────────────────────────────────────

    /** @test */
    public function it_matches_products_by_name()
    {
        $match = $this->makeProduct(['name' => 'Samsung 55 inch QLED TV']);
        $other = $this->makeProduct(['name' => 'Bosch Washing Machine']);

        $res = $this->get('/search?searchTerm=QLED');

        $res->assertOk();
        $res->assertViewIs('products.search');
        $ids = $this->shownIds($res);
        $this->assertContains($match->id, $ids);
        $this->assertNotContains($other->id, $ids);
        $this->assertSame('QLED', $res->viewData('searchTerm'));
    }

    /** @test */
    public function it_matches_products_by_brand()
    {
        $hisense = $this->makeProduct(['name' => 'Chest Freezer 200L', 'brand' => 'Hisense']);
        $lg      = $this->makeProduct(['name' => 'Chest Freezer 300L', 'brand' => 'LG']);

        $res = $this->get('/search?searchTerm=Hisense');

        $ids = $this->shownIds($res);
        $this->assertContains($hisense->id, $ids);
        $this->assertNotContains($lg->id, $ids);
    }

    /** @test */
    public function it_matches_products_by_category_name()
    {
        $cat = Category::create(['name' => 'Refrigerators', 'is_active' => true]);
        $inCat  = $this->makeProduct(['name' => 'Double Door Fridge', 'category_id' => $cat->id]);
        $notInCat = $this->makeProduct(['name' => 'Ceiling Fan']);

        $res = $this->get('/search?searchTerm=Refrigerators');

        $ids = $this->shownIds($res);
        $this->assertContains($inCat->id, $ids);
        $this->assertNotContains($notInCat->id, $ids);
    }

    /** @test */
    public function a_name_prefix_match_ranks_above_a_mid_string_match()
    {
        // Term "Sonic". "Sonic Blaster" is a name-prefix hit (rank 0);
        // "Ultra Sonic Cleaner" only matches mid-string (rank 1).
        $midString = $this->makeProduct(['name' => 'Ultra Sonic Cleaner']);
        $prefix    = $this->makeProduct(['name' => 'Sonic Blaster Speaker']);

        $res = $this->get('/search?searchTerm=Sonic');

        $ids = $this->shownIds($res);
        $this->assertSame(
            array_search($prefix->id, $ids),
            0,
            'Name-prefix match should sort first under relevance ordering'
        );
        $this->assertContains($midString->id, $ids);
    }

    // ── Wildcard / empty ────────────────────────────────────────────────────

    /** @test */
    public function an_empty_term_browses_all_active_products()
    {
        $a = $this->makeProduct();
        $b = $this->makeProduct();
        $inactive = $this->makeProduct(['is_active' => false]);

        $res = $this->get('/search?searchTerm=');

        $res->assertOk();
        $ids = $this->shownIds($res);
        $this->assertContains($a->id, $ids);
        $this->assertContains($b->id, $ids);
        $this->assertNotContains($inactive->id, $ids);
        $this->assertSame('', $res->viewData('searchTerm'));
    }

    /** @test */
    public function a_wildcard_star_browses_all_active_products()
    {
        $a = $this->makeProduct();

        $res = $this->get('/search?searchTerm=*');

        $res->assertOk();
        $this->assertContains($a->id, $this->shownIds($res));
    }

    // ── Guardrails ────────────────────────────────────────────────────────────

    /** @test */
    public function a_single_character_term_is_rejected_with_an_error_and_no_results()
    {
        $this->makeProduct(['name' => 'Anything']);

        $res = $this->get('/search?searchTerm=a');

        $res->assertOk();
        $this->assertSame(0, $res->viewData('products')->total());
        $this->assertNotEmpty($res->viewData('error'));
    }

    /** @test */
    public function search_never_returns_inactive_products()
    {
        $inactive = $this->makeProduct(['name' => 'Discontinued Widget', 'is_active' => false]);

        $res = $this->get('/search?searchTerm=Widget');

        $this->assertNotContains($inactive->id, $this->shownIds($res));
    }

    // ── Filtering ───────────────────────────────────────────────────────────

    /** @test */
    public function the_brand_filter_narrows_search_results()
    {
        $lg   = $this->makeProduct(['name' => 'Inverter AC 1.5HP', 'brand' => 'LG']);
        $midea = $this->makeProduct(['name' => 'Inverter AC 2HP', 'brand' => 'Midea']);

        $res = $this->get('/search?searchTerm=Inverter&brands[]=LG');

        $ids = $this->shownIds($res);
        $this->assertContains($lg->id, $ids);
        $this->assertNotContains($midea->id, $ids);
    }

    /** @test */
    public function the_price_filter_narrows_search_results_by_sell_price()
    {
        $cheap = $this->makeProduct(['name' => 'Budget Blender', 'price' => 15000]);
        $dear  = $this->makeProduct(['name' => 'Premium Blender', 'price' => 150000]);

        $res = $this->get('/search?searchTerm=Blender&max_price=50000');

        $ids = $this->shownIds($res);
        $this->assertContains($cheap->id, $ids);
        $this->assertNotContains($dear->id, $ids);
    }

    // ── Suggestions endpoint ──────────────────────────────────────────────────

    /** @test */
    public function the_suggestions_endpoint_returns_matching_products_and_categories()
    {
        $cat     = Category::create(['name' => 'Microwaves', 'is_active' => true]);
        $product = $this->makeProduct(['name' => 'Micro Steamer', 'category_id' => $cat->id]);

        $res = $this->getJson('/search-suggestions?query=Micro');

        $res->assertOk();
        $json = $res->json();

        $names = array_column($json, 'name');
        $this->assertContains('Micro Steamer', $names);
        $this->assertContains('Microwaves', $names);

        // Each suggestion is tagged with its type so the UI can route the click.
        $types = array_column($json, 'type');
        $this->assertContains('product', $types);
        $this->assertContains('category', $types);
    }

    /** @test */
    public function product_suggestions_carry_a_thumbnail_image()
    {
        $product = $this->makeProduct(['name' => 'Imaged Speaker']);
        \App\Models\Image::create(['product_id' => $product->id, 'image_url' => 'products/speaker.jpg']);

        $res = $this->getJson('/search-suggestions?query=Imaged');

        $res->assertOk();
        $product = collect($res->json())->firstWhere('name', 'Imaged Speaker');
        $this->assertNotNull($product);
        // The product suggestion exposes an image URL for the dropdown thumbnail.
        $this->assertStringContainsString('speaker.jpg', $product['image']);
    }

    /** @test */
    public function a_product_suggestion_without_an_image_returns_null_image()
    {
        $this->makeProduct(['name' => 'Imageless Kettle']);

        $res = $this->getJson('/search-suggestions?query=Imageless');

        $res->assertOk();
        $product = collect($res->json())->firstWhere('name', 'Imageless Kettle');
        $this->assertNotNull($product);
        $this->assertArrayHasKey('image', $product); // key always present…
        $this->assertNull($product['image']);         // …but null so the UI shows the icon
    }

    /** @test */
    public function the_suggestions_endpoint_returns_an_empty_list_without_a_query()
    {
        $this->makeProduct(['name' => 'Anything']);

        $res = $this->getJson('/search-suggestions');

        $res->assertOk();
        $res->assertExactJson([]);
    }
}
