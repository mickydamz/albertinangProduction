<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class WeightController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $categories             = Category::with(['subcategories', 'products'])->get();
        $weightThresholdKg      = (float) Setting::get('truck_weight_threshold_kg', 30);
        $orderValueThresholdNgn = (float) Setting::get('truck_order_value_threshold_ngn', 1000000);

        return view('admin.weight.index', compact('categories', 'weightThresholdKg', 'orderValueThresholdNgn'));
    }

    // ── Global thresholds ────────────────────────────────────────────────────

    public function updateThresholds(Request $request)
    {
        $request->validate([
            'truck_weight_threshold_kg'       => 'required|numeric|min:1|max:99999',
            'truck_order_value_threshold_ngn' => 'required|numeric|min:0',
        ]);

        Setting::set('truck_weight_threshold_kg',       $request->truck_weight_threshold_kg);
        Setting::set('truck_order_value_threshold_ngn', $request->truck_order_value_threshold_ngn);
        Setting::clearCache();

        return back()->with('success', 'Delivery thresholds updated.');
    }

    // ── Category weights ─────────────────────────────────────────────────────

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate(['estimated_weight_kg' => 'required|numeric|min:0|max:99999']);
        $category->update(['estimated_weight_kg' => $request->estimated_weight_kg]);
        return back()->with('success', "Weight for '{$category->name}' set to {$request->estimated_weight_kg} kg.");
    }

    public function clearCategory(Category $category)
    {
        $category->update(['estimated_weight_kg' => null]);
        return back()->with('success', "Weight cleared for category '{$category->name}'.");
    }

    public function clearCategorySubcategories(Category $category)
    {
        $count = $category->subcategories()->whereNotNull('estimated_weight_kg')->count();
        $category->subcategories()->update(['estimated_weight_kg' => null]);
        return back()->with('success', "Cleared weight overrides from {$count} subcategory(s) under '{$category->name}'.");
    }

    // ── Subcategory weights ──────────────────────────────────────────────────

    public function updateSubcategory(Request $request, Subcategory $Subcategory)
    {
        $request->validate(['estimated_weight_kg' => 'required|numeric|min:0|max:99999']);
        $Subcategory->update(['estimated_weight_kg' => $request->estimated_weight_kg]);
        return back()->with('success', "Weight for '{$Subcategory->name}' set to {$request->estimated_weight_kg} kg.");
    }

    public function clearSubcategory(Subcategory $Subcategory)
    {
        $Subcategory->update(['estimated_weight_kg' => null]);
        return back()->with('success', "Weight cleared for subcategory '{$Subcategory->name}'.");
    }

    // ── Product weights ──────────────────────────────────────────────────────

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate(['weight_kg' => 'required|numeric|min:0|max:99999']);
        $product->update(['weight_kg' => $request->weight_kg]);
        return back()->with('success', "Weight for '{$product->name}' set to {$request->weight_kg} kg.");
    }

    public function clearProduct(Product $product)
    {
        $product->update(['weight_kg' => null]);
        return back()->with('success', "Weight cleared for product '{$product->name}'.");
    }
}
