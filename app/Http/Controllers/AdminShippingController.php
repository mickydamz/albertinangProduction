<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class AdminShippingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /** Central shipping settings page — grouped by state. */
    public function index()
    {
        $locations = Location::with('state')->orderBy('name')->get();
        $grouped   = $locations->groupBy(fn ($l) => $l->state?->name ?? 'No State')->sortKeys();

        $states=\App\Models\State::orderBy('name')->get();
        return view('admin.shipping.index', compact('grouped','states'));
    }

    /** Bulk-save standard and truck delivery costs per location. */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'states' => 'nullable|array', 'states.*' => 'required|boolean',
            'areas' => 'nullable|array', 'areas.*' => 'required|boolean',
            'shipping'       => 'nullable|array',
            'shipping.*'     => 'nullable|numeric|min:0|max:99999999',
            'truck_shipping' => 'nullable|array',
            'truck_shipping.*' => 'nullable|numeric|min:0|max:99999999',
        ]);

        $shipping      = $validated['shipping']       ?? [];
        $truckShipping = $validated['truck_shipping']  ?? [];

        $ids = array_unique(array_merge(array_keys($shipping), array_keys($truckShipping)));

        \Illuminate\Support\Facades\DB::transaction(function()use($validated,$ids,$shipping,$truckShipping){
        foreach($validated['states']??[] as $id=>$enabled)\App\Models\State::whereKey($id)->update(['delivery_enabled'=>(bool)$enabled]);
        foreach($validated['areas']??[] as $id=>$enabled)Location::whereKey($id)->update(['is_active'=>(bool)$enabled]);
        foreach ($ids as $locationId) {
            $data = [];

            if (array_key_exists($locationId, $shipping)) {
                $v = $shipping[$locationId];
                $data['shipping_cost'] = ($v !== null && $v !== '') ? $v : null;
            }

            if (array_key_exists($locationId, $truckShipping)) {
                $v = $truckShipping[$locationId];
                $data['truck_shipping_cost'] = ($v !== null && $v !== '') ? $v : null;
            }

            if ($data) {
                Location::where('id', $locationId)->update($data);
            }
        }

        });
        \Illuminate\Support\Facades\Cache::forget('ref_locations');
        return back()->with('success', 'Delivery settings saved. Collection stores are unchanged.');
    }
}
