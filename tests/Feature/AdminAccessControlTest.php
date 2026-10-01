<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * The admin area is gated by the `role:admin` middleware (CheckRole):
 * guests are sent to login, logged-in non-admins get 403, admins get in.
 */
class AdminAccessControlTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /** @test */
    public function a_guest_is_redirected_to_login_from_the_admin_area()
    {
        $this->get('/admin/users')->assertRedirect(route('login'));
    }

    /** @test */
    public function a_regular_user_is_forbidden_from_the_admin_area()
    {
        $this->actingAs(User::factory()->create()) // default (non-admin) role
            ->get('/admin/users')->assertForbidden();
    }

    /** @test */
    public function a_non_admin_cannot_perform_admin_writes()
    {
        // The guard runs before the controller, so a POST is blocked outright.
        $this->actingAs(User::factory()->create())
            ->post('/admin/categories', ['name' => 'Hacked'])
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'Hacked']);
    }

    /** @test */
    public function an_admin_can_reach_the_admin_area()
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/users')->assertOk();
    }
}
