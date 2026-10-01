<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * The admin "Require Email Verification" toggle persists to settings. It is off
 * by default; enforcement of the flow is a separate follow-up.
 */
class AdminEmailVerificationSettingTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        return $user;
    }

    /** @test */
    public function admin_can_turn_email_verification_on()
    {
        $this->actingAs($this->admin())->put(route('admin.settings.update'), [
            'store_name'                 => 'Albertina',
            'email_verification_enabled' => '1',
        ])->assertRedirect();

        Setting::clearCache();
        $this->assertSame('1', Setting::get('email_verification_enabled'));
    }

    /** @test */
    public function email_verification_defaults_to_off_when_the_toggle_is_absent()
    {
        $this->actingAs($this->admin())->put(route('admin.settings.update'), [
            'store_name' => 'Albertina',
            // checkbox unchecked -> not submitted
        ])->assertRedirect();

        Setting::clearCache();
        $this->assertSame('0', Setting::get('email_verification_enabled'));
    }
}
