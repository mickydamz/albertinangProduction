<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\User;
class SupplierProductController extends Controller
{
    // Display a listing of the products
    public function index()
    {
        // Get products of the logged-in supplier and paginate them (10 products per page in this case)
    $products = Auth::user()->products()->paginate(10); 

    // Pass the products to the view
    return view('supplier.products.index', compact('products'));

    }

    // Show the form for creating a new product
    
    public function showStorefront($supplierId)
{
    // Find the supplier by ID, ensuring the user has the 'supplier' role
    $supplier = User::where('role', 'supplier')->findOrFail($supplierId);

    // Get the products for the given supplier ID, eager load 'supplier' and 'images' relationships
    $products = Product::where('supplier_id', $supplierId)
                       ->with(['supplier', 'images'])  // Eager loading relationships
                       ->get();

    // Pass supplier and products to the view for display
    return view('supplier.products.storefront', compact('supplier', 'products'));
}



    // public function showStorefront($supplierId)
    // {
    //     $supplier = User::where('role', 'supplier')->findOrFail($supplierId);

    // // Get the products for the given supplier ID
    // $products = Product::where('supplier_id', $supplierId)->get();
    // Product::with(['supplier', 'images'])->findOrFail($id);

    // // Pass supplier and products to the view for display
    // return view('supplier.products.storefront', compact('supplier', 'products'));
    // }
    
    public function show($product)
    {
        // Check if the product exists
        $product = Product::withReviewStats()->findOrFail($product);  // Assuming you are fetching the product by ID
    
        return view('supplier.products.show', compact('product'));
    }

    

public function showCarousel($productId)
{
    $product = Product::with('images')->findOrFail($productId);

    return view('supplier.products.carousel', compact('product'));
}


    public function create()
    {
        $categories = Category::all(); // Fetch all categories from the database
        return view('supplier.products.create', compact('categories')); // Pass categories to the view
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'category_id' => 'numeric',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate each image file
        ]);
    
        // Handle image upload
        // if ($request->hasFile('image')) {
        //     $imagePath = $request->file('image')->store('products', 'public'); // Save image to 'public/products' directory
        // }
    
       $product =  Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'supplier_id' => Auth::id(),
            // 'image' => $imagePath, // Save the image path
        ]);

          // Handle multiple image uploads
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('products', 'public');
            $product->images()->create(['image_url' => $imagePath]);
        }
    }
    
        return redirect()->route('supplier.products.index')->with('success', 'Product created successfully.');
    }
    

    // Show the form for editing the specified product
    public function edit(Product $product)
    {
        return view('supplier.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',  // Validate images
            'removed_images' => 'nullable|string', // Added to handle the removed images
        ]);
    
        // Update product details
        $product->update($request->only('name', 'description', 'price', 'stock'));
    
        // Handle the deletion of selected images
        if ($request->has('removed_images')) {
            // Get the removed images as an array
            $removedImages = explode(',', $request->removed_images);
            
            // Loop through each removed image
            foreach ($removedImages as $imageId) {
                $image = $product->images()->find($imageId);
    
                if ($image) {
                    // Delete the image from storage
                    \Storage::delete('public/' . $image->image_url);
                    
                    // Delete the image record from the database
                    $image->delete();
                }
            }
        }
    
        // Ensure that there is at least one image (either existing or new)
        $remainingImages = $product->images()->count();  // Count remaining images after deletion
        if ($remainingImages === 0 && !$request->hasFile('images')) {
            // If no images remain and no new images are uploaded, return an error
            return back()->withErrors(['images' => 'You must upload at least one image for the product.']);
        }
    
        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                $product->images()->create(['image_url' => $imagePath]);
            }
        }
    
        return redirect()->route('supplier.products.index')->with('success', 'Product updated successfully.');
    }
    
public function destroy(Product $product)
{
    // Delete all associated images from storage
    foreach ($product->images as $image) {
        \Storage::delete('public/' . $image->image_url);  // Delete the image file from storage
        $image->delete();  // Delete the image record from the database
    }

    // Finally, delete the product itself
    $product->delete();

    return redirect()->route('supplier.products.index')->with('success', 'Product deleted successfully.');
}

}

