<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * User-facing PRODUCTS index (ProductController@index, route products.index).
 *
 * /products delegates to the faceted search with no term (browse-all), and a
 * ?brands[] param short-circuits to that brand's page.
 */
class ProductsIndexPageTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
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
    public function it_browses_all_active_products()
    {
        $active   = $this->makeProduct();
        $inactive = $this->makeProduct(['is_active' => false]);

        $res = $this->get('/products');

        $res->assertOk();
        $res->assertViewIs('products.search');
        $ids = $this->shownIds($res);
        $this->assertContains($active->id, $ids);
        $this->assertNotContains($inactive->id, $ids);
    }

    /** @test */
    public function a_known_brand_filter_redirects_to_the_brand_page()
    {
        Brand::create(['name' => 'Bosch', 'slug' => 'bosch', 'is_active' => true]);

        $this->get('/products?brands[]=Bosch')
            ->assertRedirect(route('brand.show', 'bosch'));
    }
}
