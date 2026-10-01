<?php

namespace Tests\Feature\Auth;

use App\Models\Country;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Registration here (Auth\RegisterController) requires a country. Deeper
 * country/state validation is covered by Tests\Feature\RegistrationTest.
 */
class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $country = Country::firstOrCreate(['name' => 'Registerland'], ['iso_code' => 'RGL']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'reg_' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'country_id' => $country->id,
            'state_id' => null,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect();
    }
}
