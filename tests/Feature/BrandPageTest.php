<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * User-facing BRAND page (ProductController@showBrand, route brand.show).
 *
 * /brand/<slug> resolves a Brand by slug or name, shows only that brand's
 * ACTIVE products, and 404s on an unknown brand.
 */
class BrandPageTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function makeBrand(string $name, string $slug): Brand
    {
        return Brand::create(['name' => $name, 'slug' => $slug, 'is_active' => true]);
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

    private function shownIds(\Illuminate\Testing\TestResponse $res): array
    {
        return $res->viewData('products')->pluck('id')->all();
    }

    /** @test */
    public function it_shows_active_products_for_the_brand()
    {
        $this->makeBrand('LG', 'lg');
        $lg    = $this->makeProduct(['brand' => 'LG']);
        $sony  = $this->makeProduct(['brand' => 'Sony']);

        $res = $this->get('/brand/lg');

        $res->assertOk();
        $res->assertViewIs('products.brand');
        $ids = $this->shownIds($res);
        $this->assertContains($lg->id, $ids);
        $this->assertNotContains($sony->id, $ids);
        $this->assertSame('LG', $res->viewData('brand')->name);
    }

    /** @test */
    public function it_hides_inactive_products_of_the_brand()
    {
        $this->makeBrand('Hisense', 'hisense');
        $active   = $this->makeProduct(['brand' => 'Hisense']);
        $inactive = $this->makeProduct(['brand' => 'Hisense', 'is_active' => false]);

        $res = $this->get('/brand/hisense');

        $ids = $this->shownIds($res);
        $this->assertContains($active->id, $ids);
        $this->assertNotContains($inactive->id, $ids);
    }

    /** @test */
    public function an_unknown_brand_returns_404()
    {
        $this->get('/brand/no-such-brand')->assertNotFound();
    }
}
