<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandAssignmentController extends Controller
{
    public function index()
    {
        // Brands from the brands table (with product counts)
        $dbBrands = Brand::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(function ($brand) {
                // Prefer manager stored directly on brand, fallback to products
                $managerId = $brand->manager_id
                    ?? Product::where('brand_id', $brand->id)
                        ->whereNotNull('manager_id')
                        ->value('manager_id');

                return [
                    'brand'      => $brand->name,
                    'total'      => $brand->products_count,
                    'manager_id' => $managerId,
                ];
            });

        // Legacy brands stored as plain strings on products (no brand_id)
        $legacyBrands = Product::select(
                            'brand',
                            DB::raw('COUNT(*) as total'),
                            DB::raw('MAX(manager_id) as manager_id')
                        )
                        ->whereNotNull('brand')
                        ->where('brand', '!=', '')
                        ->whereNull('brand_id')
                        ->groupBy('brand')
                        ->orderBy('brand')
                        ->get()
                        ->map(fn($b) => [
                            'brand'      => $b->brand,
                            'total'      => $b->total,
                            'manager_id' => $b->manager_id,
                        ]);

        // Merge, deduplicate by brand name (db brands take priority)
        $brands = $dbBrands->merge($legacyBrands)
            ->unique('brand')
            ->sortBy('brand')
            ->values();

        $managers = User::where('role', 'manager')->get();

        return view('admin.brands.assign', compact('brands', 'managers'));
    }

    // public function assign(Request $request)
    // {
    //     $request->validate([
    //         'brand'      => 'required|string',
    //         'manager_id' => 'nullable|exists:users,id',
    //     ]);

    //     $managerId = $request->manager_id ?: null;

    //     $brand = Brand::where('name', $request->brand)->first();

    //     // Always save manager directly on the Brand record
    //     // so brands with zero products can still be assigned
    //     if ($brand) {
    //         $brand->manager_id = $managerId;
    //         $brand->save();
    //     }

    //     // Also update all products under this brand (by string or brand_id)
    //     $query = Product::where('brand', $request->brand);
    //     if ($brand) {
    //         $query->orWhere('brand_id', $brand->id);
    //     }
    //     $updated = $query->update(['manager_id' => $managerId]);

    //     $productNote = $updated
    //         ? " ({$updated} product(s) updated)"
    //         : " (no products yet — manager saved on brand)";

    //     return back()->with('success', "Brand '{$request->brand}' assigned successfully.{$productNote}");
    // }


    public function assign(Request $request)
{
    $request->validate([
        'brand'      => 'required|string',
        'manager_id' => 'nullable|exists:users,id',
    ]);

    $managerId = $request->manager_id ?: null;
    $brandName = trim($request->brand);

    // Ensure a Brand record exists (creates one for legacy string-only brands)
    $brand = Brand::firstOrCreate(
        ['name' => $brandName],
        [
            'manager_id' => $managerId,
            'slug'       => \Illuminate\Support\Str::slug($brandName),
            'is_active'  => true,
        ]
    );

    // Set manager explicitly to cover the "brand already existed" case
    $brand->manager_id = $managerId;
    $brand->save();

    // Backdate ALL existing products for this brand (by string OR brand_id)
    $updated = Product::where(function ($q) use ($brandName, $brand) {
        $q->whereRaw('TRIM(brand) = ?', [$brandName])
          ->orWhere('brand_id', $brand->id);
    })->update(['manager_id' => $managerId]);

    // Link legacy string-only products to the Brand record
    Product::whereRaw('TRIM(brand) = ?', [$brandName])
        ->whereNull('brand_id')
        ->update(['brand_id' => $brand->id]);

    $productNote = $updated
        ? " ({$updated} product(s) updated)"
        : " (no products yet — manager saved on brand)";

    return back()->with('success', "Brand '{$brandName}' assigned successfully.{$productNote}");
}
}