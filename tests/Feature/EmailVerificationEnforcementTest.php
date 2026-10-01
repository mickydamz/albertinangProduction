<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Enforcement of email verification, gated by the admin setting
 * `email_verification_enabled`. Off (default) = no change; on = new users must
 * verify and unverified users are blocked from checkout.
 */
class EmailVerificationEnforcementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware(ThrottleRequests::class);
        Setting::clearCache();
    }

    protected function tearDown(): void
    {
        // Never let the setting leak into other test classes via the static cache.
        Setting::clearCache();
        parent::tearDown();
    }

    private function enableVerification(): void
    {
        Setting::set('email_verification_enabled', '1');
    }

    // ── Gating ──────────────────────────────────────────────────────────────

    /** @test */
    public function with_verification_off_an_unverified_user_is_not_blocked_from_checkout()
    {
        // off by default. Empty body -> passes the gate, then fails validation (422),
        // proving the verified.optional middleware let it through.
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->postJson('/paystack/save-checkout', [])
            ->assertStatus(422);
    }

    /** @test */
    public function with_verification_on_an_unverified_user_is_blocked_from_checkout()
    {
        $this->enableVerification();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->postJson('/paystack/save-checkout', [])
            ->assertStatus(403);
    }

    /** @test */
    public function with_verification_on_a_verified_user_can_reach_checkout()
    {
        $this->enableVerification();
        $user = User::factory()->create(); // factory default = verified

        // Passes the gate; empty body then fails validation (422), not 403.
        $this->actingAs($user)
            ->postJson('/paystack/save-checkout', [])
            ->assertStatus(422);
    }

    /** @test */
    public function with_verification_on_the_checkout_page_redirects_unverified_users_to_verify()
    {
        $this->enableVerification();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/checkout')
            ->assertRedirect(route('verification.notice'));
    }

    // ── Registration ────────────────────────────────────────────────────────

    /** @test */
    public function registration_with_verification_on_sends_the_link_and_parks_on_the_notice()
    {
        $this->enableVerification();
        $country = Country::firstOrCreate(['name' => 'Verifyland'], ['iso_code' => 'VFY']);
        $email   = 'verify_' . uniqid() . '@example.com';

        $response = $this->post('/register', [
            'name' => 'Verify User',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'country_id' => $country->id,
            'state_id' => null,
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', $email)->firstOrFail();
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    /** @test */
    public function registration_with_verification_off_logs_in_without_a_link()
    {
        // off by default
        $country = Country::firstOrCreate(['name' => 'Openland'], ['iso_code' => 'OPN']);
        $email   = 'open_' . uniqid() . '@example.com';

        $this->post('/register', [
            'name' => 'Open User',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'country_id' => $country->id,
            'state_id' => null,
        ]);

        $this->assertAuthenticated();
        $user = User::where('email', $email)->firstOrFail();
        Notification::assertNotSentTo($user, VerifyEmailNotification::class);
    }

    // ── Enabling the setting ──────────────────────────────────────────────────

    /** @test */
    public function turning_verification_on_marks_existing_unverified_users_verified()
    {
        $existing = User::factory()->unverified()->create();

        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_name' => 'Albertina',
            'email_verification_enabled' => '1',
        ])->assertRedirect();

        // The existing customer is not locked out — only new signups must verify.
        $this->assertTrue($existing->fresh()->hasVerifiedEmail());
    }

    // ── Verifying ─────────────────────────────────────────────────────────────

    /** @test */
    public function a_signed_verify_link_marks_the_email_verified()
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $this->actingAs($user)->get($url)->assertRedirect();

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
