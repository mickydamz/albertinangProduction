<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\PickupPoint;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // GET /api/locations — only returns active locations
   // GET /api/locations
    // GET /api/locations
    public function index(Request $request)
    {
        $query = Location::where('is_active', true);

        // For delivery dropdown: filter by state
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->query('state_id'));
        }

        // For pickup modal: ONLY show locations that have actual physical pickup points
        if ($request->filled('for_pickup')) {
            $query->has('pickupPoints'); 
        }

        return response()->json(
            $query->orderBy('name')
                  ->get(['id', 'name', 'shipping_cost', 'truck_shipping_cost'])
        );
    }
    
    
    
    // GET /api/states
    public function states()
    {
        return response()->json(
            \App\Models\State::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    // GET /api/pickup-points?location_id=3
    public function pickupPoints(Request $request)
    {
        $request->validate(['location_id' => 'required|integer|exists:locations,id']);

        // Also guard: reject if the location itself is inactive
        $location = Location::where('id', $request->location_id)
            ->where('is_active', true)
            ->first();

        if (!$location) {
            return response()->json(['message' => 'Location not available.'], 404);
        }

        $points = PickupPoint::where('location_id', $location->id)
            ->get(['id', 'location_id', 'name', 'address', 'hours']);

        return response()->json($points);
    }
    
    
}