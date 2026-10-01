<?php
// app/Http/Controllers/AdminPickupPointController.php
namespace App\Http\Controllers;

use App\Models\PickupPoint;
use App\Models\Location;
use Illuminate\Http\Request;

class AdminPickupPointController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $pickupPoints = PickupPoint::with('location')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('location', fn ($l) => $l->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('location_id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pickup_points.index', compact('pickupPoints', 'search'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        return view('admin.pickup_points.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'hours'       => 'nullable|string|max:255',
        ]);

        PickupPoint::create($request->only('location_id', 'name', 'address', 'hours'));

        return redirect()->route('admin.pickup-points.index')
            ->with('success', 'Pickup point created successfully!');
    }

    public function edit(PickupPoint $pickupPoint)
    {
        $locations = Location::orderBy('name')->get();
        return view('admin.pickup_points.edit', compact('pickupPoint', 'locations'));
    }

    public function update(Request $request, PickupPoint $pickupPoint)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'hours'       => 'nullable|string|max:255',
        ]);

        $pickupPoint->update($request->only('location_id', 'name', 'address', 'hours'));

        return redirect()->route('admin.pickup-points.index')
            ->with('success', 'Pickup point updated successfully!');
    }

    public function destroy(PickupPoint $pickupPoint)
    {
        $pickupPoint->delete();

        return redirect()->route('admin.pickup-points.index')
            ->with('success', 'Pickup point deleted successfully!');
    }
}