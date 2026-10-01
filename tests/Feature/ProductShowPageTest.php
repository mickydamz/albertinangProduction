<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * User-facing PRODUCT detail page (ProductController@show, route product.show).
 */
class ProductShowPageTest extends TestCase
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

    /** @test */
    public function it_renders_the_product_detail_page()
    {
        $product = $this->makeProduct();

        $res = $this->get("/product/{$product->id}");

        $res->assertOk();
        $res->assertViewIs('products.show');
        $this->assertSame($product->id, $res->viewData('product')->id);
    }

    /** @test */
    public function a_missing_product_returns_404()
    {
        $this->get('/product/99999999')->assertNotFound();
    }
}
