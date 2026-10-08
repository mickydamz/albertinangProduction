<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Ensure the country/state reference data these tests rely on exists,
     * regardless of whether the test database was seeded. firstOrCreate keeps
     * this idempotent when the real seed data is already present.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $nigeria = Country::firstOrCreate(['name' => 'Nigeria'], ['iso_code' => 'NGA']);
        City::firstOrCreate(['name' => 'Rivers', 'country_id' => $nigeria->id]);

        $ghana = Country::firstOrCreate(['name' => 'Ghana'], ['iso_code' => 'GHA']);
        City::firstOrCreate(['name' => 'Greater Accra', 'country_id' => $ghana->id]);

        // A country with no cities, for the "no states" path.
        Country::firstOrCreate(['name' => 'Statelandia'], ['iso_code' => 'STL']);
    }

    private function postRegister(array $overrides = []): \Illuminate\Testing\TestResponse
    {
        return $this->post(route('register'), array_merge([
            'name'                  => 'Test User',
            'email'                 => 'test_' . uniqid() . '@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'country_id'            => null,
            'state_id'              => null,
        ], $overrides));
    }

    private function nigeria(): Country
    {
        return Country::where('name', 'Nigeria')->firstOrFail();
    }

    private function ghana(): Country
    {
        return Country::where('name', 'Ghana')->firstOrFail();
    }

    private function noStateCountry(): Country
    {
        return Country::whereNotIn('id', City::select('country_id'))->firstOrFail();
    }

    public function test_registration_saves_full_address_to_customer_account(): void
    {
        $country=$this->nigeria();
        $state=City::where('country_id',$country->id)->firstOrFail();
        $email='address_'.uniqid().'@example.com';
        $this->postRegister(['email'=>$email,'country_id'=>$country->id,'state_id'=>$state->id,
            'shipping_address'=>"12 Test Street\nFlat 2",'city'=>'Port Harcourt','postal_code'=>'500001'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users',['email'=>$email,'shipping_address'=>"12 Test Street\nFlat 2",
            'city'=>'Port Harcourt','postal_code'=>'500001','state'=>$state->name,'country'=>'Nigeria']);
    }

    public function test_registration_rejects_oversized_address_and_restores_input(): void
    {
        $country=$this->nigeria();
        $state=City::where('country_id',$country->id)->firstOrFail();
        $email='invalid_address_'.uniqid().'@example.com';
        $this->from('/register')->postRegister(['email'=>$email,'country_id'=>$country->id,'state_id'=>$state->id,
            'shipping_address'=>str_repeat('x',256),'city'=>'Port Harcourt'])
            ->assertSessionHasErrors('shipping_address')->assertSessionHasInput('city','Port Harcourt');
        $this->assertDatabaseMissing('users',['email'=>$email]);
    }

    // ── 1. Valid Nigeria + Rivers registers ────────────────────────────────────

    /** @test */
    public function valid_nigeria_and_state_registers_successfully()
    {
        $nigeria = $this->nigeria();
        $state   = City::where('country_id', $nigeria->id)
                       ->where('name', 'like', '%Rivers%')
                       ->first()
                   ?? City::where('country_id', $nigeria->id)->first();

        $email = 'ng_test_' . uniqid() . '@example.com';

        $response = $this->postRegister([
            'email'      => $email,
            'country_id' => $nigeria->id,
            'state_id'   => $state->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email'      => $email,
            'country_id' => $nigeria->id,
            'state_id'   => $state->id,
        ]);
    }

    // ── 2. State from another country is rejected ──────────────────────────────

    /** @test */
    public function state_from_wrong_country_is_rejected()
    {
        $nigeria     = $this->nigeria();
        $ghanaState  = City::where('country_id', $this->ghana()->id)->firstOrFail();

        $response = $this->postRegister([
            'country_id' => $nigeria->id,
            'state_id'   => $ghanaState->id,   // Ghana state submitted with Nigeria selected
        ]);

        $response->assertSessionHasErrors('state_id');
        $this->assertDatabaseMissing('users', ['state_id' => $ghanaState->id]);
    }

    // ── 3. Country with no states registers without state_id ──────────────────

    /** @test */
    public function country_with_no_states_registers_without_state_id()
    {
        $country = $this->noStateCountry();
        $email   = 'nostate_' . uniqid() . '@example.com';

        $response = $this->postRegister([
            'email'      => $email,
            'country_id' => $country->id,
            'state_id'   => null,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email'      => $email,
            'country_id' => $country->id,
            'state_id'   => null,
        ]);
    }

    // ── 4. Invalid country_id is rejected ─────────────────────────────────────

    /** @test */
    public function invalid_country_id_is_rejected()
    {
        $response = $this->postRegister([
            'country_id' => 999999,
        ]);

        $response->assertSessionHasErrors('country_id');
    }
}
