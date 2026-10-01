<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminLocationController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $locations = Location::with('state')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('state', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.locations.index', compact('locations', 'search'));
    }

    public function create()
    {
        $states = State::orderBy('name')->get();
        return view('admin.locations.create', compact('states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255|unique:locations,name',
            'state_id'            => 'nullable|exists:states,id',
            'shipping_cost'       => 'nullable|numeric|min:0',
            'truck_shipping_cost' => 'nullable|numeric|min:0',
        ]);

        Location::create([
            'name'                => $request->name,
            'state_id'            => $request->state_id,
            'shipping_cost'       => $request->shipping_cost,
            'truck_shipping_cost' => $request->truck_shipping_cost,
            'is_active'           => true,
        ]);
        Cache::forget('ref_locations');

        // If this form was opened from another page (e.g. the State edit page),
        // return there instead of the locations list. Only accept same-host URLs.
        $redirectTo = (string) $request->input('redirect_to', '');
        if ($redirectTo !== '' && str_starts_with($redirectTo, url('/'))) {
            return redirect()->to($redirectTo)
                ->with('success', 'Location created successfully!');
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully!');
    }

    public function show(Location $location)
    {
        $location->load('pickupPoints');
        return view('admin.locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        $states = State::orderBy('name')->get();
        return view('admin.locations.edit', compact('location', 'states'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name'                => 'required|string|max:255|unique:locations,name,' . $location->id,
            'state_id'            => 'nullable|exists:states,id',
            'is_active'           => 'nullable|boolean',
            'shipping_cost'       => 'nullable|numeric|min:0',
            'truck_shipping_cost' => 'nullable|numeric|min:0',
        ]);

        $location->name                = $request->name;
        $location->state_id            = $request->state_id;
        $location->is_active           = $request->boolean('is_active');
        $location->shipping_cost       = $request->shipping_cost;
        $location->truck_shipping_cost = $request->truck_shipping_cost;
        $location->save();
        Cache::forget('ref_locations');

        // If the edit was opened from another page (e.g. the State edit page),
        // return there instead of the locations list. Only accept same-host URLs.
        $redirectTo = (string) $request->input('redirect_to', '');
        if ($redirectTo !== '' && str_starts_with($redirectTo, url('/'))) {
            return redirect()->to($redirectTo)
                ->with('success', 'Location updated successfully!');
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully!');
    }

    /**
     * Toggle a location active/inactive.
     * Route: PATCH /admin/locations/{location}/toggle-active
     */
    public function toggleActive(Location $location)
    {
        $location->is_active = !$location->is_active;
        $location->save();
        Cache::forget('ref_locations');

        $status = $location->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.locations.index')
            ->with('success', "Location \"{$location->name}\" {$status} successfully!");
    }

    public function destroy(Location $location)
    {
        $location->delete();
        Cache::forget('ref_locations');

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully!');
    }
}