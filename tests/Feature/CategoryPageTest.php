<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * User-facing CATEGORY page (ProductController@showCategory, route category.show).
 *
 * A shopper opens /category/<slug>. The controller must:
 *   • resolve the slug to a Category OR a Subcategory (hyphens → spaces),
 *   • scope to that category's products AND its subcategories' products,
 *   • show only ACTIVE products,
 *   • honour brand / price / availability filters and sorting,
 *   • 404 on an unknown slug.
 *
 * We assert against the view data (categoryProducts paginator) rather than the
 * rendered HTML, so the tests describe behaviour, not markup.
 *
 * DatabaseTransactions rolls back every write (same pattern as CheckoutDeliveryTest).
 */
class CategoryPageTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // nav_categories / ref_* are Cache::remember()'d for an hour; flush so a
        // previous test's (rolled-back) rows can't leak into this one's nav.
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeCategory(string $name): Category
    {
        return Category::create(['name' => $name, 'is_active' => true]);
    }

    private function makeSubcategory(string $name, Category $parent): Subcategory
    {
        return Subcategory::create([
            'name'        => $name,
            'category_id' => $parent->id,
            'is_active'   => true,
        ]);
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

    /** The product IDs the category view will render. */
    private function shownIds(\Illuminate\Testing\TestResponse $res): array
    {
        return $res->viewData('categoryProducts')->pluck('id')->all();
    }

    // ── Resolution ──────────────────────────────────────────────────────────

    /** @test */
    public function it_shows_active_products_of_a_category_and_its_subcategories()
    {
        $cat = $this->makeCategory('Televisions');
        $sub = $this->makeSubcategory('Smart TVs', $cat);

        $directProduct = $this->makeProduct(['category_id' => $cat->id]);
        $subProduct    = $this->makeProduct(['category_id' => $cat->id, 'subcategory_id' => $sub->id]);

        $res = $this->get('/category/televisions');

        $res->assertOk();
        $res->assertViewIs('category');
        $ids = $this->shownIds($res);
        $this->assertContains($directProduct->id, $ids);
        $this->assertContains($subProduct->id, $ids);
        $this->assertSame('Televisions', $res->viewData('categoryName'));
    }

    /** @test */
    public function it_hides_inactive_products()
    {
        $cat = $this->makeCategory('Refrigerators');
        $active   = $this->makeProduct(['category_id' => $cat->id]);
        $inactive = $this->makeProduct(['category_id' => $cat->id, 'is_active' => false]);

        $res = $this->get('/category/refrigerators');

        $ids = $this->shownIds($res);
        $this->assertContains($active->id, $ids);
        $this->assertNotContains($inactive->id, $ids);
    }

    /** @test */
    public function a_hyphenated_slug_resolves_to_a_multi_word_category_name()
    {
        $cat = $this->makeCategory('Home Appliances');
        $product = $this->makeProduct(['category_id' => $cat->id]);

        $res = $this->get('/category/home-appliances');

        $res->assertOk();
        $this->assertContains($product->id, $this->shownIds($res));
        $this->assertSame('Home Appliances', $res->viewData('categoryName'));
    }

    /** @test */
    public function a_subcategory_slug_scopes_to_that_subcategory_and_exposes_the_parent()
    {
        $cat = $this->makeCategory('Air Cooling');
        $sub = $this->makeSubcategory('Standing Fans', $cat);

        $inSub    = $this->makeProduct(['category_id' => $cat->id, 'subcategory_id' => $sub->id]);
        $catOnly  = $this->makeProduct(['category_id' => $cat->id]); // parent cat, no sub

        $res = $this->get('/category/standing-fans');

        $res->assertOk();
        $ids = $this->shownIds($res);
        $this->assertContains($inSub->id, $ids);
        $this->assertNotContains($catOnly->id, $ids, 'Subcategory page must not pull in sibling category-only products');

        $this->assertSame('Standing Fans', $res->viewData('categoryName'));
        $this->assertSame($cat->id, $res->viewData('parentCategory')->id);
    }

    /** @test */
    public function an_unknown_slug_returns_404()
    {
        $this->get('/category/this-category-does-not-exist')->assertNotFound();
    }

    // ── Filtering & sorting ───────────────────────────────────────────────────

    /** @test */
    public function the_brand_filter_narrows_the_results()
    {
        $cat = $this->makeCategory('Sound and Vision');
        $lg  = $this->makeProduct(['category_id' => $cat->id, 'brand' => 'LG']);
        $sony = $this->makeProduct(['category_id' => $cat->id, 'brand' => 'Sony']);

        $res = $this->get('/category/sound-and-vision?brands[]=LG');

        $ids = $this->shownIds($res);
        $this->assertContains($lg->id, $ids);
        $this->assertNotContains($sony->id, $ids);
    }

    /** @test */
    public function the_price_filter_narrows_by_sell_price()
    {
        $cat   = $this->makeCategory('Kitchen Appliances');
        $cheap = $this->makeProduct(['category_id' => $cat->id, 'price' => 20000]);
        $dear  = $this->makeProduct(['category_id' => $cat->id, 'price' => 200000]);

        $res = $this->get('/category/kitchen-appliances?min_price=100000');

        $ids = $this->shownIds($res);
        $this->assertContains($dear->id, $ids);
        $this->assertNotContains($cheap->id, $ids);
    }

    /** @test */
    public function it_can_sort_by_price_ascending()
    {
        $cat  = $this->makeCategory('Garment Care');
        $mid  = $this->makeProduct(['category_id' => $cat->id, 'price' => 50000]);
        $low  = $this->makeProduct(['category_id' => $cat->id, 'price' => 10000]);
        $high = $this->makeProduct(['category_id' => $cat->id, 'price' => 90000]);

        $res = $this->get('/category/garment-care?sort_by=price-asc');

        $this->assertSame(
            [$low->id, $mid->id, $high->id],
            $this->shownIds($res)
        );
    }

    /** @test */
    public function the_availability_filter_keeps_only_in_stock_products()
    {
        $cat      = $this->makeCategory('Generators');
        $inStock  = $this->makeProduct(['category_id' => $cat->id, 'stock' => 5]);
        $outStock = $this->makeProduct(['category_id' => $cat->id, 'stock' => 0]);

        $res = $this->get('/category/generators?availability[]=in-stock');

        $ids = $this->shownIds($res);
        $this->assertContains($inStock->id, $ids);
        $this->assertNotContains($outStock->id, $ids);
    }

    /** @test */
    public function the_max_price_filter_narrows_by_sell_price()
    {
        $cat   = $this->makeCategory('Inverters');
        $cheap = $this->makeProduct(['category_id' => $cat->id, 'price' => 20000]);
        $dear  = $this->makeProduct(['category_id' => $cat->id, 'price' => 200000]);

        $res = $this->get('/category/inverters?max_price=100000');

        $ids = $this->shownIds($res);
        $this->assertContains($cheap->id, $ids);
        $this->assertNotContains($dear->id, $ids);
    }

    /** @test */
    public function min_and_max_price_bound_the_results_from_both_ends()
    {
        $cat  = $this->makeCategory('Blenders');
        $low  = $this->makeProduct(['category_id' => $cat->id, 'price' => 10000]);
        $mid  = $this->makeProduct(['category_id' => $cat->id, 'price' => 50000]);
        $high = $this->makeProduct(['category_id' => $cat->id, 'price' => 120000]);

        $res = $this->get('/category/blenders?min_price=30000&max_price=90000');

        $ids = $this->shownIds($res);
        $this->assertContains($mid->id, $ids);
        $this->assertNotContains($low->id, $ids);
        $this->assertNotContains($high->id, $ids);
    }

    /** @test */
    public function multiple_brands_are_ored_together()
    {
        $cat  = $this->makeCategory('Home Theaters');
        $lg   = $this->makeProduct(['category_id' => $cat->id, 'brand' => 'LG']);
        $sony = $this->makeProduct(['category_id' => $cat->id, 'brand' => 'Sony']);
        $jbl  = $this->makeProduct(['category_id' => $cat->id, 'brand' => 'JBL']);

        $res = $this->get('/category/home-theaters?brands[]=LG&brands[]=Sony');

        $ids = $this->shownIds($res);
        $this->assertContains($lg->id, $ids);
        $this->assertContains($sony->id, $ids);
        $this->assertNotContains($jbl->id, $ids);
    }

    /** @test */
    public function brand_and_price_filters_combine_as_and()
    {
        $cat = $this->makeCategory('Sound Bar');
        $lgCheap  = $this->makeProduct(['category_id' => $cat->id, 'brand' => 'LG',   'price' => 20000]);
        $lgDear   = $this->makeProduct(['category_id' => $cat->id, 'brand' => 'LG',   'price' => 200000]);
        $sonyDear = $this->makeProduct(['category_id' => $cat->id, 'brand' => 'Sony', 'price' => 200000]);

        // LG brand AND price >= 100k → only the dear LG qualifies.
        $res = $this->get('/category/sound-bar?brands[]=LG&min_price=100000');

        $ids = $this->shownIds($res);
        $this->assertContains($lgDear->id, $ids);
        $this->assertNotContains($lgCheap->id, $ids, 'cheap LG fails the price bound');
        $this->assertNotContains($sonyDear->id, $ids, 'dear Sony fails the brand filter');
    }

    /** @test */
    public function it_can_sort_by_price_descending()
    {
        $cat  = $this->makeCategory('Juicers');
        $mid  = $this->makeProduct(['category_id' => $cat->id, 'price' => 50000]);
        $low  = $this->makeProduct(['category_id' => $cat->id, 'price' => 10000]);
        $high = $this->makeProduct(['category_id' => $cat->id, 'price' => 90000]);

        $res = $this->get('/category/juicers?sort_by=price-desc');

        $this->assertSame([$high->id, $mid->id, $low->id], $this->shownIds($res));
    }

    /** @test */
    public function it_can_sort_by_newest()
    {
        $cat = $this->makeCategory('Ceiling Fan');
        $old = $this->makeProduct(['category_id' => $cat->id]);
        $mid = $this->makeProduct(['category_id' => $cat->id]);
        $new = $this->makeProduct(['category_id' => $cat->id]);

        // Stagger created_at explicitly so "newest" is deterministic.
        \Illuminate\Support\Facades\DB::table('products')->where('id', $old->id)->update(['created_at' => now()->subDays(3)]);
        \Illuminate\Support\Facades\DB::table('products')->where('id', $mid->id)->update(['created_at' => now()->subDays(2)]);
        \Illuminate\Support\Facades\DB::table('products')->where('id', $new->id)->update(['created_at' => now()->subDay()]);

        $res = $this->get('/category/ceiling-fan?sort_by=newest');

        $this->assertSame([$new->id, $mid->id, $old->id], $this->shownIds($res));
    }

    /** @test */
    public function it_paginates_at_twenty_products_per_page()
    {
        $cat = $this->makeCategory('Standing Fans');
        for ($i = 0; $i < 21; $i++) {
            $this->makeProduct(['category_id' => $cat->id]);
        }

        $res = $this->get('/category/standing-fans');

        $paginator = $res->viewData('categoryProducts');
        $this->assertSame(20, $paginator->count(), 'first page shows 20 items');
        $this->assertSame(21, $paginator->total(), 'total reflects every matching product');
    }
}
