<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin dashboard analytics JSON endpoints (AdminDashboardController) — the
 * data feeds behind the dashboard charts.
 */
class AdminDashboardDataTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** @test */
    public function every_dashboard_analytics_endpoint_responds()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $endpoints = [
            '/admin/data/user-analytics',
            '/admin/data/transaction-analytics',
            '/admin/data/product-analytics',
            '/admin/data/revenue-analytics',
            '/admin/data/order-status-analytics',
        ];

        foreach ($endpoints as $endpoint) {
            $this->assertSame(
                200,
                $this->actingAs($admin)->getJson($endpoint)->status(),
                "GET {$endpoint} should respond (200)"
            );
        }
    }

    /** @test */
    public function a_non_admin_cannot_read_dashboard_analytics()
    {
        $this->actingAs(User::factory()->create())
            ->getJson('/admin/data/revenue-analytics')
            ->assertStatus(403);
    }
}
