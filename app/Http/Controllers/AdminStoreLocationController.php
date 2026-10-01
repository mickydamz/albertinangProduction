<?php

namespace App\Http\Controllers;

use App\Models\StoreLocation;
use Illuminate\Http\Request;

class AdminStoreLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $locations = StoreLocation::when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderByDesc('is_hq')->orderBy('sort_order')->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.store_locations.index', compact('locations', 'search'));
    }

    public function create()
    {
        return view('admin.store_locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'required|string|max:500',
            'phone'      => 'nullable|string|max:60',
            'email'      => 'nullable|email|max:255',
            'hours'      => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_hq'      => 'nullable|boolean',
        ]);

        StoreLocation::create(array_merge(
            $request->only('name', 'address', 'phone', 'email', 'hours', 'sort_order'),
            ['is_hq' => $request->boolean('is_hq')]
        ));

        return redirect()->route('admin.store-locations.index')
            ->with('success', 'Store location created successfully!');
    }

    public function edit(StoreLocation $storeLocation)
    {
        return view('admin.store_locations.edit', compact('storeLocation'));
    }

    public function update(Request $request, StoreLocation $storeLocation)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'required|string|max:500',
            'phone'      => 'nullable|string|max:60',
            'email'      => 'nullable|email|max:255',
            'hours'      => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_hq'      => 'nullable|boolean',
        ]);

        $storeLocation->update(array_merge(
            $request->only('name', 'address', 'phone', 'email', 'hours', 'sort_order'),
            ['is_hq' => $request->boolean('is_hq')]
        ));

        return redirect()->route('admin.store-locations.index')
            ->with('success', 'Store location updated successfully!');
    }

    public function destroy(StoreLocation $storeLocation)
    {
        $storeLocation->delete();

        return redirect()->route('admin.store-locations.index')
            ->with('success', 'Store location deleted successfully!');
    }
}
