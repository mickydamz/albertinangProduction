<?php
// app/Http/Controllers/AdminPickupPointController.php
namespace App\Http\Controllers;

use App\Models\PickupPoint;
use App\Models\State;
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
        $states = State::orderBy('name')->get();
        return view('admin.pickup_points.create', compact('states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'state_id' => 'required|exists:states,id',
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:500',
            'hours'       => 'nullable|string|max:255',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function()use($request){
            $directory=\App\Models\StoreLocation::create($request->only('name','address','hours'));
            PickupPoint::create($request->only('state_id', 'name', 'address', 'hours') + ['is_active'=>true,'store_location_id'=>$directory->id]);
        });

        return redirect()->route('admin.shopping-locations.index')
            ->with('success', 'Pickup point created successfully!');
    }

    public function edit(PickupPoint $pickupPoint)
    {
        $states = State::orderBy('name')->get();
        return view('admin.pickup_points.edit', compact('pickupPoint', 'states'));
    }

    public function update(Request $request, PickupPoint $pickupPoint)
    {
        $request->validate([
            'state_id' => 'required|exists:states,id',
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:500',
            'hours'       => 'nullable|string|max:255',
        ]);

        if ((int)$request->state_id !== (int)$pickupPoint->state_id && \Illuminate\Support\Facades\DB::table('product_pickup_point')->where('pickup_point_id',$pickupPoint->id)->exists()) return back()->withErrors(['state_id'=>'This store has product assignments. Create a new store in the new state and review assignments first.']);
        $pickupPoint->update($request->only('state_id', 'name', 'address', 'hours'));

        return redirect()->route('admin.shopping-locations.index')
            ->with('success', 'Pickup point updated successfully!');
    }

    public function destroy(PickupPoint $pickupPoint)
    {
        if (\Illuminate\Support\Facades\DB::table('product_pickup_point')->where('pickup_point_id',$pickupPoint->id)->exists() || \Illuminate\Support\Facades\DB::table('origin_delivery_areas')->where('pickup_point_id',$pickupPoint->id)->exists() || \App\Models\Order::where('pickup_point_id',$pickupPoint->id)->orWhere('dispatch_point_id',$pickupPoint->id)->exists()) return back()->withErrors(['point'=>'This store is referenced by products, routes or orders. Disable it in Shopping locations instead.']);
        $pickupPoint->delete();

        return redirect()->route('admin.shopping-locations.index')
            ->with('success', 'Pickup point deleted successfully!');
    }
}
