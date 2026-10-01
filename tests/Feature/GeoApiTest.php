<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\PickupPoint;
use App\Models\State;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

/**
 * Public geo API used by the checkout delivery/pickup selectors
 * (LocationController): /api/states, /api/locations, /api/pickup-points.
 */
class GeoApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function location(array $attrs = []): Location
    {
        return Location::create(array_merge([
            'name'                => 'Location ' . uniqid(),
            'is_active'           => true,
            'shipping_cost'       => 1500,
            'truck_shipping_cost' => 5000,
        ], $attrs));
    }

    // ── /api/states ─────────────────────────────────────────────────────────────

    /** @test */
    public function the_states_endpoint_returns_only_active_states()
    {
        State::create(['name' => 'Lagos', 'is_active' => true]);
        State::create(['name' => 'Ghost State', 'is_active' => false]);

        $res = $this->getJson('/api/states');

        $res->assertOk();
        $names = collect($res->json())->pluck('name')->all();
        $this->assertContains('Lagos', $names);
        $this->assertNotContains('Ghost State', $names);
    }

    // ── /api/locations ──────────────────────────────────────────────────────────

    /** @test */
    public function the_locations_endpoint_returns_active_locations_with_their_fees()
    {
        $active   = $this->location(['name' => 'Ikeja', 'shipping_cost' => 1500]);
        $inactive = $this->location(['name' => 'Closed Branch', 'is_active' => false]);

        $res = $this->getJson('/api/locations');

        $res->assertOk();
        $rows = collect($res->json());
        $this->assertContains($active->id, $rows->pluck('id')->all());
        $this->assertNotContains($inactive->id, $rows->pluck('id')->all());

        $row = $rows->firstWhere('id', $active->id);
        $this->assertArrayHasKey('shipping_cost', $row);
        $this->assertArrayHasKey('truck_shipping_cost', $row);
        $this->assertEquals(1500, (float) $row['shipping_cost']);
    }

    /** @test */
    public function locations_can_be_filtered_by_state()
    {
        $stateA = State::create(['name' => 'State A', 'is_active' => true]);
        $stateB = State::create(['name' => 'State B', 'is_active' => true]);
        $inA = $this->location(['state_id' => $stateA->id]);
        $inB = $this->location(['state_id' => $stateB->id]);

        $ids = collect($this->getJson('/api/locations?state_id=' . $stateA->id)->json())->pluck('id')->all();

        $this->assertContains($inA->id, $ids);
        $this->assertNotContains($inB->id, $ids);
    }

    /** @test */
    public function for_pickup_only_returns_locations_that_have_pickup_points()
    {
        $withPoints    = $this->location(['name' => 'Has Points']);
        $withoutPoints = $this->location(['name' => 'No Points']);
        PickupPoint::create(['location_id' => $withPoints->id, 'name' => 'Point 1', 'address' => '123 St', 'hours' => '9-5']);

        $ids = collect($this->getJson('/api/locations?for_pickup=1')->json())->pluck('id')->all();

        $this->assertContains($withPoints->id, $ids);
        $this->assertNotContains($withoutPoints->id, $ids);
    }

    // ── /api/pickup-points ──────────────────────────────────────────────────────

    /** @test */
    public function pickup_points_require_a_location_id()
    {
        $this->getJson('/api/pickup-points')->assertStatus(422);
    }

    /** @test */
    public function pickup_points_reject_a_nonexistent_location()
    {
        $this->getJson('/api/pickup-points?location_id=99999999')->assertStatus(422);
    }

    /** @test */
    public function pickup_points_return_404_for_an_inactive_location()
    {
        $loc = $this->location(['is_active' => false]);

        $this->getJson('/api/pickup-points?location_id=' . $loc->id)->assertStatus(404);
    }

    /** @test */
    public function pickup_points_are_scoped_to_the_requested_active_location()
    {
        $loc   = $this->location(['name' => 'Active']);
        $other = $this->location(['name' => 'Other']);
        PickupPoint::create(['location_id' => $loc->id,   'name' => 'Mall Pickup',  'address' => '5 Mall Rd', 'hours' => '10-8']);
        PickupPoint::create(['location_id' => $other->id, 'name' => 'Other Pickup', 'address' => '9 Other Rd', 'hours' => '9-6']);

        $res = $this->getJson('/api/pickup-points?location_id=' . $loc->id);

        $res->assertOk();
        $names = collect($res->json())->pluck('name')->all();
        $this->assertContains('Mall Pickup', $names);
        $this->assertNotContains('Other Pickup', $names);
    }
}
