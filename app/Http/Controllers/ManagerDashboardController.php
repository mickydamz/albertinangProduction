<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManagerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'manager']);
    }

    private function ownedProducts()
    {
        return Product::where('manager_id', auth()->id());
    }

    private function findOwned(int $id): Product
    {
        return $this->ownedProducts()->findOrFail($id);
    }

    public function index(Request $request)
    {
        $totalProducts = $this->ownedProducts()->count();
        $totalBrands   = $this->ownedProducts()->whereNotNull('brand')->distinct('brand')->count('brand');
        $inStock       = $this->ownedProducts()->where('stock', '>', 0)->count();
        $outOfStock    = $this->ownedProducts()->where('stock', '<=', 0)->count();

        $brands = $this->ownedProducts()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->select('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        // Always define $activeBrand, even if null
        $activeBrand = $request->query('brand');

        // Validate that the requested brand exists in the user's products
        if ($activeBrand && !$brands->contains($activeBrand)) {
            $activeBrand = null;
        }

        $products = $this->ownedProducts()
            ->with('images')
            ->when($activeBrand, function ($q) use ($activeBrand) {
                return $q->where('brand', $activeBrand);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('manager.dashboard', compact(
            'totalProducts', 'totalBrands', 'inStock', 'outOfStock',
            'brands', 'activeBrand', 'products'
        ));
    }

    public function edit(Product $product)
    {
        // Ensure the manager owns this product
        $product = $this->findOwned($product->id);

        return view('manager.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Ensure the manager owns this product
        $product = $this->findOwned($product->id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('manager.products.index')
                         ->with('success', 'Product updated successfully.');
    }
}
