<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * The "Verified" (email) column in the admin users list only appears when
 * email verification is enabled in Settings.
 */
class AdminUsersVerifiedColumnTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::clearCache();
    }

    protected function tearDown(): void
    {
        Setting::clearCache();
        parent::tearDown();
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        return $user;
    }

    /** @test */
    public function verified_column_is_hidden_when_email_verification_is_off()
    {
        // off by default
        User::factory()->create();

        $this->actingAs($this->admin())
            ->get('/admin/users')
            ->assertOk()
            ->assertDontSee('Verified');
    }

    /** @test */
    public function verified_column_shows_when_email_verification_is_on()
    {
        Setting::set('email_verification_enabled', '1');

        User::factory()->create();                    // verified (factory default)
        User::factory()->unverified()->create();      // unverified

        $this->actingAs($this->admin())
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Verified')
            ->assertSee('Unverified');
    }
}
