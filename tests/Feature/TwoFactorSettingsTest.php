<?php

namespace Tests\Feature;

use App\Http\Middleware\TwoFactorAuth;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Two-factor settings (TwoFactorSettingsController) — the account page where a
 * user turns 2FA on/off. Enabling generates + emails a code; disabling clears it.
 */
class TwoFactorSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Bypass the 2FA challenge middleware so we hit the settings controller
        // directly — a pending code would otherwise redirect us to /2fa.
        $this->withoutMiddleware([ThrottleRequests::class, TwoFactorAuth::class]);
        Mail::fake();
    }

    /** @test */
    public function the_two_factor_settings_page_renders()
    {
        $this->actingAs(User::factory()->create())
            ->get('/account/two-factor')
            ->assertOk()
            ->assertViewIs('account.two-factor-settings');
    }

    /** @test */
    public function enabling_two_factor_generates_a_code_and_emails_it()
    {
        $user = User::factory()->create(['two_factor_code' => null]);

        $this->actingAs($user)->post('/account/two-factor/enable')
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertNotNull($user->fresh()->two_factor_code);
        Mail::assertSent(TwoFactorCodeMail::class, fn ($m) => $m->hasTo($user->email));
    }

    /** @test */
    public function disabling_two_factor_clears_the_code()
    {
        $user = User::factory()->create([
            'two_factor_code'       => '123456',
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($user)->post('/account/two-factor/disable')
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertNull($user->fresh()->two_factor_code);
    }

    /** @test */
    public function a_guest_cannot_change_two_factor_settings()
    {
        $this->post('/account/two-factor/enable')->assertRedirect(route('login'));
    }
}
