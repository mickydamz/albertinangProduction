<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class SupplierDashboardController extends Controller
{
    // Show the supplier dashboard
    public function index()
{
    $supplierId = auth()->id();

    // Key metrics
    $totalProducts = Product::where('supplier_id', $supplierId)->count();
    $lowStockProducts = Product::where('supplier_id', $supplierId)
                               ->where('stock', '<', 10) // Assuming 'stock' column
                               ->count();
    $recentOrdersCount = Transaction::whereHas('products', function ($query) use ($supplierId) {
        $query->where('supplier_id', $supplierId);
    })->whereDate('created_at', '>=', now()->subMonth())->count();
    $monthlySales = Transaction::whereHas('products', function ($query) use ($supplierId) {
        $query->where('supplier_id', $supplierId);
    })->whereDate('created_at', '>=', now()->subMonth())->sum('total_amount'); // Assuming 'total_amount' field

    // Paginated product list
    $products = Product::where('supplier_id', $supplierId)->paginate(10);

    return view('supplier.dashboard', compact('products', 'totalProducts', 'lowStockProducts', 'recentOrdersCount', 'monthlySales'));
}


    // Show the form to create a new product
    public function create()
    {
        return view('supplier.create');
    }

    // Store a newly created product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->supplier_id = auth()->id();
        $product->save();

        return redirect()->route('supplier.dashboard')->with('success', 'Product created successfully.');
    }

    // Show the form to edit a product
    public function edit(Product $product)
    {
        return view('supplier.edit', compact('product'));
    }

    // Update the specified product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->save();

        return redirect()->route('supplier.dashboard')->with('success', 'Product updated successfully.');
    }

    // Delete a product
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('supplier.dashboard')->with('success', 'Product deleted successfully.');
    }
}
