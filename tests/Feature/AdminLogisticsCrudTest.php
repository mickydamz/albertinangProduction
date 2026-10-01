<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Location;
use App\Models\PickupPoint;
use App\Models\State;
use App\Models\StoreLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Admin logistics/config CRUD: states, locations, pickup points, store
 * locations and cities.
 */
class AdminLogisticsCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ── States ──────────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_toggle_and_delete_a_state()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/states', ['name' => 'Enugu'])->assertRedirect();
        $this->assertDatabaseHas('states', ['name' => 'Enugu']);

        $state = State::where('name', 'Enugu')->first();
        $before = (bool) $state->is_active;

        $this->actingAs($admin)->patch("/admin/states/{$state->id}/toggle-active")->assertRedirect();
        $this->assertSame(!$before, (bool) $state->fresh()->is_active);

        $this->actingAs($admin)->delete("/admin/states/{$state->id}")->assertRedirect();
        $this->assertDatabaseMissing('states', ['id' => $state->id]);
    }

    /** @test */
    public function state_names_must_be_unique()
    {
        State::create(['name' => 'Lagos', 'is_active' => true]);

        $this->actingAs($this->admin())->from('/admin/states')
            ->post('/admin/states', ['name' => 'Lagos'])
            ->assertSessionHasErrors('name');
    }

    // ── Locations ───────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_toggle_and_delete_a_location()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/locations', [
            'name'          => 'Ikeja',
            'shipping_cost' => 1500,
        ])->assertRedirect();
        $this->assertDatabaseHas('locations', ['name' => 'Ikeja']);

        $loc = Location::where('name', 'Ikeja')->first();
        $before = (bool) $loc->is_active;

        $this->actingAs($admin)->patch("/admin/locations/{$loc->id}/toggle-active")->assertRedirect();
        $this->assertSame(!$before, (bool) $loc->fresh()->is_active);

        $this->actingAs($admin)->delete("/admin/locations/{$loc->id}")->assertRedirect();
        $this->assertDatabaseMissing('locations', ['id' => $loc->id]);
    }

    // ── Pickup points ─────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_and_delete_a_pickup_point()
    {
        $admin = $this->admin();
        $loc   = Location::create(['name' => 'Wuse', 'is_active' => true]);

        $this->actingAs($admin)->post('/admin/pickup-points', [
            'location_id' => $loc->id,
            'name'        => 'Wuse Market Point',
            'address'     => 'Shop 5, Wuse Market',
        ])->assertRedirect();
        $this->assertDatabaseHas('pickup_points', ['name' => 'Wuse Market Point', 'location_id' => $loc->id]);

        $point = PickupPoint::where('name', 'Wuse Market Point')->first();
        $this->actingAs($admin)->delete("/admin/pickup-points/{$point->id}")->assertRedirect();
        $this->assertDatabaseMissing('pickup_points', ['id' => $point->id]);
    }

    /** @test */
    public function a_pickup_point_requires_a_valid_location()
    {
        $this->actingAs($this->admin())->from('/admin/pickup-points/create')
            ->post('/admin/pickup-points', ['name' => 'No Location', 'address' => 'x'])
            ->assertSessionHasErrors('location_id');
    }

    // ── Store locations ─────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_and_delete_a_store_location()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/store-locations', [
            'name'    => 'Enugu HQ',
            'address' => '45 Zik Ave, Enugu',
        ])->assertRedirect();
        $this->assertDatabaseHas('store_locations', ['name' => 'Enugu HQ']);

        $store = StoreLocation::where('name', 'Enugu HQ')->first();
        $this->actingAs($admin)->delete("/admin/store-locations/{$store->id}")->assertRedirect();
        $this->assertDatabaseMissing('store_locations', ['id' => $store->id]);
    }

    // ── Cities ──────────────────────────────────────────────────────────────────

    /** @test */
    public function an_admin_can_create_and_delete_a_city()
    {
        $admin   = $this->admin();
        $country = Country::firstOrCreate(['name' => 'Testland'], ['iso_code' => 'TLD']);

        $this->actingAs($admin)->post('/admin/cities', [
            'name'       => 'Test City',
            'country_id' => $country->id,
        ])->assertRedirect();
        $this->assertDatabaseHas('cities', ['name' => 'Test City', 'country_id' => $country->id]);

        $city = City::where('name', 'Test City')->first();
        $this->actingAs($admin)->delete("/admin/cities/{$city->id}")->assertRedirect();
        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }
}
