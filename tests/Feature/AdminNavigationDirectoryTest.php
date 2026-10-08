<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class AdminNavigationDirectoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_every_directory_destination_renders_and_is_unique(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        $admin = User::factory()->create(['role' => 'admin']);
        $routes = [];
        foreach (config('admin_navigation') as $group) {
            foreach ($group['items'] as $item) {
                $this->assertNotContains($item['route'], $routes);
                $routes[] = $item['route'];
                $this->actingAs($admin)->get(route($item['route']))->assertOk();
            }
        }
    }

    public function test_nested_order_page_highlights_orders_and_brand_assignment_has_its_own_link(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        $admin = User::factory()->create(['role' => 'admin']);
        $html = $this->actingAs($admin)->get(route('admin.orders.create'))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/href="[^"]*\/admin\/orders"[^>]*aria-current="page"/', $html);
        $this->assertStringContainsString('Find an admin page', $html);
        $this->assertStringContainsString('Brand assignments', $html);
        $this->assertStringContainsString('Payments &amp; invoices', $html);
    }

    public function test_dashboard_analytics_load_on_the_local_database(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        $admin = User::factory()->create(['role' => 'admin']);
        foreach (['revenue', 'user', 'transaction', 'product', 'order-status'] as $chart) {
            $this->actingAs($admin)->getJson('/admin/data/'.$chart.'-analytics')
                ->assertOk()->assertJsonStructure(['labels', 'datasets']);
        }
    }
}
