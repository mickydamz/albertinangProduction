<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\PickupPoint;
use App\Models\State;
use App\Models\StoreLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Search + pagination on the delivery admin index pages
 * (States, Delivery Locations, Pickup Points, Store Locations).
 */
class AdminDeliveryListingTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        return $user;
    }

    private function state(string $name): State
    {
        return State::create(['name' => $name, 'is_active' => true]);
    }

    private function location(string $name, ?State $state = null): Location
    {
        return Location::create([
            'name'                => $name,
            'state_id'            => ($state ?? $this->state('S_' . uniqid()))->id,
            'is_active'           => true,
            'shipping_cost'       => 3500,
            'truck_shipping_cost' => 20000,
        ]);
    }

    // ── States ────────────────────────────────────────────────────────────────

    /** @test */
    public function states_index_renders_and_search_filters()
    {
        $keep = 'Lagos_' . uniqid();
        $drop = 'Kano_' . uniqid();
        $this->state($keep);
        $this->state($drop);

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.states.index'))->assertOk();

        $this->actingAs($admin)->get(route('admin.states.index', ['search' => $keep]))
            ->assertOk()
            ->assertSee($keep)
            ->assertDontSee($drop);
    }

    /** @test */
    public function states_index_paginates_at_15_per_page()
    {
        $tag = 'Pg' . substr(uniqid(), -6);
        // 16 states with deterministic ordering (name asc): _01 .. _16
        for ($i = 1; $i <= 16; $i++) {
            $this->state($tag . '_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT));
        }

        $admin = $this->admin();

        // Page 1 holds the first 15 (…_01 … _15); the 16th spills to page 2.
        $this->actingAs($admin)->get(route('admin.states.index', ['search' => $tag]))
            ->assertOk()
            ->assertSee($tag . '_01')
            ->assertDontSee($tag . '_16');

        $this->actingAs($admin)->get(route('admin.states.index', ['search' => $tag, 'page' => 2]))
            ->assertOk()
            ->assertSee($tag . '_16');
    }

    // ── Delivery Locations ──────────────────────────────────────────────────────

    /** @test */
    public function locations_index_renders_and_search_filters_by_name_and_state()
    {
        $state = $this->state('Rivers_' . uniqid());
        $keep  = 'PortHarcourt_' . uniqid();
        $drop  = 'Ikeja_' . uniqid();
        $this->location($keep, $state);
        $this->location($drop);

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.locations.index'))->assertOk();

        // By location name
        $this->actingAs($admin)->get(route('admin.locations.index', ['search' => $keep]))
            ->assertOk()
            ->assertSee($keep)
            ->assertDontSee($drop);

        // By state name (relation)
        $this->actingAs($admin)->get(route('admin.locations.index', ['search' => $state->name]))
            ->assertOk()
            ->assertSee($keep);
    }

    // ── Pickup Points ────────────────────────────────────────────────────────────

    /** @test */
    public function pickup_points_index_renders_and_search_filters()
    {
        $location = $this->location('Loc_' . uniqid());
        $keep = 'Depot_' . uniqid();
        $drop = 'Kiosk_' . uniqid();
        PickupPoint::create(['name' => $keep, 'location_id' => $location->id, 'address' => '1 Test Rd']);
        PickupPoint::create(['name' => $drop, 'location_id' => $location->id, 'address' => '2 Test Rd']);

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.pickup-points.index'))->assertOk();

        $this->actingAs($admin)->get(route('admin.pickup-points.index', ['search' => $keep]))
            ->assertOk()
            ->assertSee($keep)
            ->assertDontSee($drop);
    }

    // ── Store Locations ──────────────────────────────────────────────────────────

    /** @test */
    public function store_locations_index_renders_and_search_filters()
    {
        $keep = 'Showroom_' . uniqid();
        $drop = 'Warehouse_' . uniqid();
        StoreLocation::create(['name' => $keep, 'address' => '10 Aba Rd', 'is_hq' => true, 'sort_order' => 1]);
        StoreLocation::create(['name' => $drop, 'address' => '20 Aba Rd', 'is_hq' => false, 'sort_order' => 2]);

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.store-locations.index'))->assertOk();

        $this->actingAs($admin)->get(route('admin.store-locations.index', ['search' => $keep]))
            ->assertOk()
            ->assertSee($keep)
            ->assertDontSee($drop);
    }
}
