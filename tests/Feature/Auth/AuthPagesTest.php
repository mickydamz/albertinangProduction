<?php

namespace Tests\Feature\Auth;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Smoke coverage that every active auth page renders and the core session
 * actions (logout) work. Flow details (posting credentials, resetting, etc.)
 * live in the focused Auth\* tests; this guards that the screens themselves
 * don't break.
 */
class AuthPagesTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Setting::clearCache();
    }

    protected function tearDown(): void
    {
        Setting::clearCache();
        parent::tearDown();
    }

    // ── Guest pages ───────────────────────────────────────────────────────────

    /** @test */
    public function login_page_renders(): void
    {
        $this->get('/login')->assertOk();
    }

    /** @test */
    public function register_page_renders(): void
    {
        $this->get('/register')->assertOk();
    }

    /** @test */
    public function forgot_password_page_renders(): void
    {
        $this->get('/password/reset')->assertOk();
    }

    /** @test */
    public function reset_password_page_renders(): void
    {
        // Any token string renders the form; validity is checked on submit.
        $this->get('/password/reset/'.Password::createToken(User::factory()->create()))
            ->assertOk();
    }

    // ── Authenticated pages ─────────────────────────────────────────────────────

    /** @test */
    public function confirm_password_page_renders(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/password/confirm')
            ->assertOk();
    }

    /** @test */
    public function two_factor_page_renders(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/2fa')
            ->assertOk();
    }

    /** @test */
    public function email_verification_notice_renders_for_an_unverified_user(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get('/email/verify')
            ->assertOk();
    }

    /** @test */
    public function email_verification_notice_redirects_a_verified_user(): void
    {
        $this->actingAs(User::factory()->create()) // factory default = verified
            ->get('/email/verify')
            ->assertRedirect();
    }

    // ── Guards & session ────────────────────────────────────────────────────────

    /** @test */
    public function auth_only_pages_redirect_guests_to_login(): void
    {
        $this->get('/password/confirm')->assertRedirect(route('login'));
        $this->get('/2fa')->assertRedirect(route('login'));
        $this->get('/email/verify')->assertRedirect(route('login'));
    }

    /** @test */
    public function logout_ends_the_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect();

        $this->assertGuest();
    }
}
