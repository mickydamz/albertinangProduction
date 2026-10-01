<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Manager authentication & area access.
 *
 * Covers the real login round-trip (POST /login → LoginController@authenticated)
 * for a manager, the role-based redirect, and the ManagerMiddleware gate that
 * protects /manager/*. Product scoping itself lives in ManagerAssignmentTest;
 * this file is purely about "can the right person log in and get in".
 */
class ManagerLoginTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function manager(string $password = 'secret-password'): User
    {
        return User::factory()->create([
            'role'              => 'manager',
            'password'          => Hash::make($password),
            'email_verified_at' => now(),
        ]);
    }

    // ── Login round-trip ──────────────────────────────────────────────────────

    /** @test */
    public function a_manager_can_log_in_with_valid_credentials_and_is_sent_to_the_manager_dashboard()
    {
        $manager = $this->manager();

        $res = $this->post('/login', [
            'email'    => $manager->email,
            'password' => 'secret-password',
        ]);

        $res->assertRedirect(route('manager.dashboard'));
        $this->assertAuthenticatedAs($manager);
    }

    /** @test */
    public function a_manager_with_the_wrong_password_is_rejected_and_stays_a_guest()
    {
        $manager = $this->manager();

        $res = $this->from('/login')->post('/login', [
            'email'    => $manager->email,
            'password' => 'wrong-password',
        ]);

        $res->assertRedirect('/login');
        $res->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ── Area access after login ────────────────────────────────────────────────

    /** @test */
    public function the_manager_dashboard_redirects_to_the_manager_product_list()
    {
        $manager = $this->manager();

        $this->actingAs($manager)
            ->get('/manager/dashboard')
            ->assertRedirect(route('manager.products.index'));
    }

    /** @test */
    public function a_logged_in_manager_can_reach_the_manager_product_area()
    {
        $manager = $this->manager();

        $this->actingAs($manager)->get('/manager/products')->assertOk();
    }

    /** @test */
    public function a_guest_hitting_the_manager_area_is_redirected_to_login()
    {
        // ManagerMiddleware sends unauthenticated web requests to the login page.
        $this->get('/manager/products')->assertRedirect(route('login'));
    }

    /** @test */
    public function a_regular_user_is_forbidden_from_the_manager_area()
    {
        $user = User::factory()->create(); // default (non-manager) role

        $this->actingAs($user)->get('/manager/products')->assertStatus(403);
    }

    /** @test */
    public function an_admin_may_also_enter_the_manager_area()
    {
        // ManagerMiddleware allows both 'manager' and 'admin'.
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/manager/products')->assertOk();
    }
}
