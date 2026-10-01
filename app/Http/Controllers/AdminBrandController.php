<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminBrandController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Temporarily add this to your index method in AdminBrandController
// dd(Brand::first()->logo);

        $brands = Brand::withCount('products')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                             ->orWhere('slug', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('admin.brands.index', compact('brands', 'search'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }


// public function store(Request $request)
// {
//     $validated = $request->validate([
//         'name' => 'required|string|max:255|unique:brands,name',
//         'slug' => 'nullable|string|max:255',
//         'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
//     ]);

//     $data = [
//         'name' => $request->name,
//         'slug' => $request->filled('slug')
//             ? Str::slug($request->slug)
//             : Str::slug($request->name),
//     ];

//     if ($request->hasFile('logo')) {
//         $data['logo'] = $request->file('logo')->store('images/brands', 'public');
//     }

//     Brand::create($data);

//     return redirect()->route('admin.brands.index')
//         ->with('success', 'Brand created successfully.');
// }


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:brands,name',
        'slug' => 'nullable|string|max:255',
        'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
    ]);

    $slugSource = $request->filled('slug') ? $request->slug : $request->name;
    $slug = Str::slug($slugSource);

    $data = [
        'name' => $request->name,
        'slug' => $slug ?: Str::random(8),
    ];

    if ($request->hasFile('logo')) {
        $path = $request->file('logo')->store('images/brands', 'public');
        if (!$path) {
            return back()->withInput()->withErrors(['logo' => 'Image upload failed. Check storage permissions.']);
        }
        $data['logo'] = $path;
    }

    $data['is_active'] = $request->has('is_active');

    Brand::create($data);

    return redirect()->route('admin.brands.index')
        ->with('success', 'Brand created successfully.');
}

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255|unique:brands,name',
    //         'slug' => 'nullable|string|max:255|unique:brands,slug',
    //         'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
    //     ]);

    //     $data = [
    //         'name' => $request->name,
    //         'slug' => $request->filled('slug')
    //             ? Str::slug($request->slug)
    //             : Str::slug($request->name),
    //     ];

    //     if ($request->hasFile('logo')) {
    //         $data['logo'] = $request->file('logo')->store('images/brands', 'public');
    //     }

    //     Brand::create($data);

    //     return redirect()->route('admin.brands.index')
    //         ->with('success', 'Brand created successfully.');
    // }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

public function update(Request $request, Brand $brand)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
        'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
        'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
    ]);

    $data = [
        'name' => $request->name,
        'slug' => $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name),
    ];

    // Handle remove_logo FIRST, but only if no new file is being uploaded
    if (!$request->hasFile('logo') && $request->boolean('remove_logo') && $brand->logo) {
        if (Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }
        $data['logo'] = null;
    }

    // New upload takes priority over remove_logo
    if ($request->hasFile('logo')) {
        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }
        $data['logo'] = $request->file('logo')->store('images/brands', 'public');
    }

    $data['is_active'] = $request->has('is_active');

    $brand->update($data);

    return redirect()->route('admin.brands.index')
        ->with('success', 'Brand updated successfully.');
}

    public function destroy(Brand $brand)
    {
        // Prevent deletion if brand has associated products
        if ($brand->products()->exists()) {
            return redirect()->route('admin.brands.index')
                ->with('error', "Cannot delete \"{$brand->name}\" — it has " . $brand->products()->count() . ' product(s) attached. Reassign or delete those products first.');
        }

        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    public function toggleActive(Brand $brand)
{
    $brand->update(['is_active' => !$brand->is_active]);

    return response()->json([
        'success'   => true,
        'is_active' => $brand->is_active,
    ]);
}
}