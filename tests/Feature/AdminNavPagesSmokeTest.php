<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Smoke coverage for the admin sidebar pages that weren't otherwise tested —
 * every landing page an admin can click must render (200). Guards against a
 * controller/view change silently breaking an admin section.
 */
class AdminNavPagesSmokeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** @test */
    public function every_admin_nav_page_renders()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $paths = [
            '/admin',                  // dashboard
            '/admin/users',
            '/admin/tags',
            '/admin/sizes',
            '/admin/colors',
            '/admin/banners',
            '/admin/audit',
            '/admin/about',
            '/admin/contact',
            '/admin/faqs',
            '/admin/payment-methods',
            '/admin/cities',
            '/admin/transactions',
            '/admin/currencies',
        ];

        foreach ($paths as $path) {
            $this->assertSame(
                200,
                $this->actingAs($admin)->get($path)->status(),
                "GET {$path} should render (200)"
            );
        }
    }
}
