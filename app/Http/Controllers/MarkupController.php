<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Http\Request;

class MarkupController extends Controller
{
    public function index()
    {
        $categories = Category::with(['subcategories', 'products'])->get();
        return view('admin.markup.index', compact('categories'));
    }

    // ── MARKUP ───────────────────────────────────────────────────────────────

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate(['markup_percent' => 'required|numeric|min:0|max:1000']);
        $category->update(['markup_percent' => $request->markup_percent]);
        return back()->with('success', "Markup for category '{$category->name}' set to {$request->markup_percent}%.");
    }

    public function updateSubcategory(Request $request, Subcategory $Subcategory)
    {
        $request->validate(['markup_percent' => 'required|numeric|min:0|max:1000']);
        $Subcategory->update(['markup_percent' => $request->markup_percent]);
        return back()->with('success', "Markup for subcategory '{$Subcategory->name}' set to {$request->markup_percent}%.");
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate(['markup_percent' => 'required|numeric|min:0|max:1000']);
        $product->update(['markup_percent' => $request->markup_percent]);
        return back()->with('success', "Markup for product '{$product->name}' set to {$request->markup_percent}%.");
    }

    public function clearCategory(Category $category)
    {
        $category->update(['markup_percent' => null]);
        return back()->with('success', "Markup cleared for category '{$category->name}'.");
    }

    public function clearSubcategory(Subcategory $Subcategory)
    {
        $Subcategory->update(['markup_percent' => null]);
        return back()->with('success', "Markup cleared for subcategory '{$Subcategory->name}'.");
    }

    public function clearProduct(Product $product)
    {
        $product->update(['markup_percent' => null]);
        return back()->with('success', "Markup cleared for product '{$product->name}'.");
    }

    public function clearCategorySubcategories(Category $category)
    {
        $count = $category->subcategories()->whereNotNull('markup_percent')->count();
        $category->subcategories()->update(['markup_percent' => null]);
        return back()->with('success', "Cleared markup overrides from {$count} subcategory(s) under '{$category->name}'.");
    }

    public function bulkAll(Request $request)
    {
        $request->validate(['markup_percent' => 'required|numeric|min:0|max:1000']);

        Product::query()->update(['markup_percent' => null]);
        Subcategory::query()->update(['markup_percent' => null]);
        Category::query()->update(['markup_percent' => $request->markup_percent]);

        return back()->with('success', "Global markup of {$request->markup_percent}% applied to all " . Category::count() . " categories.");
    }

    // ── DISCOUNT ─────────────────────────────────────────────────────────────

    public function updateCategoryDiscount(Request $request, Category $category)
    {
        $request->validate(['discount_percent' => 'required|numeric|min:0|max:100']);
        $category->update(['discount_percent' => $request->discount_percent]);
        return back()->with('success', "Discount for category '{$category->name}' set to {$request->discount_percent}%.");
    }

    public function updateSubcategoryDiscount(Request $request, Subcategory $Subcategory)
    {
        $request->validate(['discount_percent' => 'required|numeric|min:0|max:100']);
        $Subcategory->update(['discount_percent' => $request->discount_percent]);
        return back()->with('success', "Discount for subcategory '{$Subcategory->name}' set to {$request->discount_percent}%.");
    }

    public function updateProductDiscount(Request $request, Product $product)
    {
        $request->validate(['discount_percent' => 'required|numeric|min:0|max:100']);
        $product->update(['discount_percent' => $request->discount_percent]);
        return back()->with('success', "Discount for product '{$product->name}' set to {$request->discount_percent}%.");
    }

    public function clearCategoryDiscount(Category $category)
    {
        $category->update(['discount_percent' => null]);
        return back()->with('success', "Discount cleared for category '{$category->name}'.");
    }

    public function clearSubcategoryDiscount(Subcategory $Subcategory)
    {
        $Subcategory->update(['discount_percent' => null]);
        return back()->with('success', "Discount cleared for subcategory '{$Subcategory->name}'.");
    }

    public function clearProductDiscount(Product $product)
    {
        $product->update(['discount_percent' => null]);
        return back()->with('success', "Discount cleared for product '{$product->name}'.");
    }

   
    public function clearCategorySubcategoryDiscounts(Category $category)
    {
        $count = $category->subcategories()->whereNotNull('discount_percent')->count();
        $category->subcategories()->update(['discount_percent' => null]);
        return back()->with('success', "Cleared discount overrides from {$count} subcategory(s) under '{$category->name}'.");
    }

    /**
     * Wipe all product + subcategory discount overrides,
     * then set every category to the given discount so the
     * cascade flows cleanly top-down — mirrors bulkAll().
     */
    public function bulkDiscount(Request $request)
    {
        $request->validate(['discount_percent' => 'required|numeric|min:0|max:100']);

        Product::query()->update(['discount_percent' => null]);
        Subcategory::query()->update(['discount_percent' => null]);
        Category::query()->update(['discount_percent' => $request->discount_percent]);

        return back()->with('success', "Global discount of {$request->discount_percent}% applied to all " . Category::count() . " categories.");
    }
}