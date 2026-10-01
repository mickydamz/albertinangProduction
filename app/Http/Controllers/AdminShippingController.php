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

        return view('admin.shipping.index', compact('grouped'));
    }

    /** Bulk-save standard and truck delivery costs per location. */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'shipping'       => 'nullable|array',
            'shipping.*'     => 'nullable|numeric|min:0|max:99999999',
            'truck_shipping' => 'nullable|array',
            'truck_shipping.*' => 'nullable|numeric|min:0|max:99999999',
        ]);

        $shipping      = $validated['shipping']       ?? [];
        $truckShipping = $validated['truck_shipping']  ?? [];

        $ids = array_unique(array_merge(array_keys($shipping), array_keys($truckShipping)));

        foreach ($ids as $locationId) {
            $data = [];

            if (array_key_exists($locationId, $shipping)) {
                $v = $shipping[$locationId];
                $data['shipping_cost'] = ($v !== null && $v !== '') ? $v : 0;
            }

            if (array_key_exists($locationId, $truckShipping)) {
                $v = $truckShipping[$locationId];
                $data['truck_shipping_cost'] = ($v !== null && $v !== '') ? $v : 0;
            }

            if ($data) {
                Location::where('id', $locationId)->update($data);
            }
        }

        return back()->with('success', 'Shipping costs updated.');
    }
}