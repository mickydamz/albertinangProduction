<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    // ── Category ──────────────────────────────────────────────────────────────

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'discount_percent'   => 'required|numeric|min:0|max:100',
            'discount_expires_at' => 'nullable|date|after:now',
        ]);

        $category->update([
            'discount_percent'    => $request->discount_percent,
            'discount_expires_at' => $request->filled('discount_expires_at')
                                        ? $request->discount_expires_at
                                        : null,
        ]);

        $msg = "Discount for '{$category->name}' set to {$request->discount_percent}%";
        $msg .= $request->filled('discount_expires_at')
            ? ', expires ' . \Carbon\Carbon::parse($request->discount_expires_at)->format('M j, Y g:ia') . '.'
            : ' (no expiry).';

        return back()->with('success', $msg);
    }

    public function clearCategory(Category $category)
    {
        $category->update(['discount_percent' => null, 'discount_expires_at' => null]);
        return back()->with('success', "Discount cleared for '{$category->name}'.");
    }

    public function clearCategorySubcategories(Category $category)
    {
        $count = $category->subcategories()->whereNotNull('discount_percent')->count();
        $category->subcategories()->update([
            'discount_percent'    => null,
            'discount_expires_at' => null,
        ]);
        return back()->with('success', "Cleared discount from {$count} subcategory(s) under '{$category->name}'.");
    }

    // ── Subcategory ───────────────────────────────────────────────────────────

    public function updateSubcategory(Request $request, Subcategory $Subcategory)
    {
        $request->validate([
            'discount_percent'    => 'required|numeric|min:0|max:100',
            'discount_expires_at' => 'nullable|date|after:now',
        ]);

        $Subcategory->update([
            'discount_percent'    => $request->discount_percent,
            'discount_expires_at' => $request->filled('discount_expires_at')
                                        ? $request->discount_expires_at
                                        : null,
        ]);

        $msg = "Discount for '{$Subcategory->name}' set to {$request->discount_percent}%";
        $msg .= $request->filled('discount_expires_at')
            ? ', expires ' . \Carbon\Carbon::parse($request->discount_expires_at)->format('M j, Y g:ia') . '.'
            : ' (no expiry).';

        return back()->with('success', $msg);
    }

    public function clearSubcategory(Subcategory $Subcategory)
    {
        $Subcategory->update(['discount_percent' => null, 'discount_expires_at' => null]);
        return back()->with('success', "Discount cleared for '{$Subcategory->name}'.");
    }

    // ── Product ───────────────────────────────────────────────────────────────

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'discount_percent'    => 'required|numeric|min:0|max:100',
            'discount_expires_at' => 'nullable|date|after:now',
        ]);

        $product->update([
            'discount_percent'    => $request->discount_percent,
            'discount_expires_at' => $request->filled('discount_expires_at')
                                        ? $request->discount_expires_at
                                        : null,
        ]);

        $msg = "Discount for '{$product->name}' set to {$request->discount_percent}%";
        $msg .= $request->filled('discount_expires_at')
            ? ', expires ' . \Carbon\Carbon::parse($request->discount_expires_at)->format('M j, Y g:ia') . '.'
            : ' (no expiry).';

        return back()->with('success', $msg);
    }

    public function clearProduct(Product $product)
    {
        $product->update(['discount_percent' => null, 'discount_expires_at' => null]);
        return back()->with('success', "Discount cleared for '{$product->name}'.");
    }

    // ── Bulk ──────────────────────────────────────────────────────────────────

    public function bulkAll(Request $request)
    {
        $request->validate([
            'discount_percent'    => 'required|numeric|min:0|max:100',
            'discount_expires_at' => 'nullable|date|after:now',
        ]);

        $expiry = $request->filled('discount_expires_at') ? $request->discount_expires_at : null;

        // Clear product & subcategory overrides, set all categories uniformly
        Product::query()->update(['discount_percent' => null, 'discount_expires_at' => null]);
        Subcategory::query()->update(['discount_percent' => null, 'discount_expires_at' => null]);
        Category::query()->update([
            'discount_percent'    => $request->discount_percent,
            'discount_expires_at' => $expiry,
        ]);

        $msg = "Global discount of {$request->discount_percent}% applied to all " . Category::count() . " categories";
        $msg .= $expiry
            ? ', expires ' . \Carbon\Carbon::parse($expiry)->format('M j, Y g:ia') . '.'
            : ' (no expiry).';

        return back()->with('success', $msg);
    }

    public function clearAll()
    {
        Product::query()->update(['discount_percent' => null, 'discount_expires_at' => null]);
        Subcategory::query()->update(['discount_percent' => null, 'discount_expires_at' => null]);
        Category::query()->update(['discount_percent' => null, 'discount_expires_at' => null]);

        return back()->with('success', 'All discounts cleared.');
    }

    // ── Index (discount manager page) ─────────────────────────────────────────

    public function index()
    {
        $categories = Category::with('subcategories')->get();

        // Find the most common active category-level discount and its expiry
        $globalRow = Category::whereNotNull('discount_percent')
            ->selectRaw('discount_percent, discount_expires_at, COUNT(*) as cnt')
            ->groupBy('discount_percent', 'discount_expires_at')
            ->orderByDesc('cnt')
            ->first();

        $globalDiscountValue    = $globalRow?->discount_percent;
        $globalDiscountExpiresAt = $globalRow?->discount_expires_at; // Carbon instance or null

        return view('admin.discount.index', compact(
            'categories',
            'globalDiscountValue',
            'globalDiscountExpiresAt'
        ));
    }
}