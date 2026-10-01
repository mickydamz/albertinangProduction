<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Smoke coverage that every public storefront page renders (HTTP 200) — guards
 * against a view/controller change breaking a page a guest can reach.
 */
class PublicPagesSmokeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** @test */
    public function every_public_page_renders()
    {
        $paths = [
            '/',                 // storefront home
            '/about',
            '/terms',
            '/privacy',
            '/faq',
            '/blog',
            '/store-locator',
            '/store-locations',
            '/contact',
            '/cart',
        ];

        foreach ($paths as $path) {
            $this->assertSame(200, $this->get($path)->status(), "GET {$path} should render (200)");
        }
    }
}
