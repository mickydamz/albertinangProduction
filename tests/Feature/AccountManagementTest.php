<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Customer account management (AccountController) — the real account screens the
 * storefront links to (/account, /account/update, /account/change-password).
 */
class AccountManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ── Pages ──────────────────────────────────────────────────────────────────

    /** @test */
    public function the_account_page_renders()
    {
        $this->actingAs(User::factory()->create())
            ->get('/account')
            ->assertOk()
            ->assertViewIs('sims.account');
    }

    /** @test */
    public function the_change_password_page_renders()
    {
        $this->actingAs(User::factory()->create())
            ->get('/account/change-password')
            ->assertOk()
            ->assertViewIs('sims.change-password');
    }

    /** @test */
    public function a_guest_cannot_reach_the_account_area()
    {
        $this->get('/account')->assertRedirect(route('login'));
    }

    // ── Profile update ─────────────────────────────────────────────────────────

    /** @test */
    public function a_user_can_update_their_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/account/update', [
            'name'     => 'Updated Name',
            'email'    => 'updated@example.com',
            'phone_no' => '08099998888',
            'city'     => 'Enugu',
        ])->assertRedirect(route('account.index'));

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
            'city'  => 'Enugu',
        ]);
    }

    /** @test */
    public function the_profile_email_must_be_unique_to_another_user()
    {
        $taken = User::factory()->create(['email' => 'taken@example.com']);
        $user  = User::factory()->create(['email' => 'mine@example.com']);

        $this->actingAs($user)->from('/account')->patch('/account/update', [
            'name'  => $user->name,
            'email' => 'taken@example.com',
        ])->assertSessionHasErrors('email');

        $this->assertSame('mine@example.com', $user->fresh()->email);
    }

    /** @test */
    public function keeping_the_same_email_on_update_is_allowed()
    {
        $user = User::factory()->create(['email' => 'same@example.com']);

        $this->actingAs($user)->patch('/account/update', [
            'name'  => 'Renamed',
            'email' => 'same@example.com', // unchanged — unique rule ignores self
        ])->assertRedirect(route('account.index'));

        $this->assertSame('Renamed', $user->fresh()->name);
    }

    // ── Change password ─────────────────────────────────────────────────────────

    /** @test */
    public function a_user_can_change_their_password_with_the_correct_current_one()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->actingAs($user)->post('/account/change-password', [
            'current_password'      => 'old-password',
            'password'              => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertRedirect(route('account.index'));

        $this->assertTrue(Hash::check('brand-new-pass', $user->fresh()->password));
    }

    /** @test */
    public function a_wrong_current_password_is_rejected_and_leaves_the_password_unchanged()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->actingAs($user)->from('/account/change-password')->post('/account/change-password', [
            'current_password'      => 'not-the-password',
            'password'              => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    /** @test */
    public function a_new_password_must_be_confirmed_and_at_least_eight_characters()
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        // Too short + mismatched confirmation.
        $this->actingAs($user)->from('/account/change-password')->post('/account/change-password', [
            'current_password'      => 'old-password',
            'password'              => 'short',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
