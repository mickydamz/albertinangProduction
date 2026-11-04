<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    // List all products
    public function index()
    {
        $products = Product::with('supplier', 'category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    // Show the form to create a new product
    public function create()
    {
        $categories = Category::all(); // Get all categories to choose from
        $suppliers = User::where('role', 'supplier')->get(); // Get all suppliers to choose from
        return view('admin.products.create', compact('categories', 'suppliers'));
    }

    // Store a new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'supplier_id' => 'required|exists:users,id',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = $request->only('name', 'description', 'price', 'supplier_id', 'stock', 'category_id');

        // Handle file upload if an image is provided
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images/products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    // Show the form to edit an existing product
    public function edit(Product $product)
    {
        $categories = Category::all(); // Get all categories
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // Update an existing product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'supplier_id' => 'required|exists:users,id',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = $request->only('name', 'description', 'price', 'supplier_id', 'stock', 'category_id');

        // Handle file upload if an image is provided
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images/products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    // Delete a product
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
