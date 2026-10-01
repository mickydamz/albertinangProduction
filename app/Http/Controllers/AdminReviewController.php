<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
class AdminReviewController extends Controller
{
    public function index(Request $request)
{
    $search = trim((string) $request->query('search', ''));

    $reviews = Review::with(['author', 'product'])
        ->when($search !== '', function ($q) use ($search) {
            $q->where('content', 'like', "%{$search}%")
              ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"))
              ->orWhereHas('author', fn ($u) => $u->where('name', 'like', "%{$search}%")
                                                  ->orWhere('email', 'like', "%{$search}%"));
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('admin.reviews.index', compact('reviews', 'search'));
}


public function create()
{
    // Eager-load images so the searchable picker can show a thumbnail per product.
    $products = Product::with('images')->orderBy('name')->get(['id', 'name']);
    $users = User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'email']);

    return view('admin.reviews.create', compact('products', 'users'));
}


public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'user_id' => 'required|exists:users,id',
        'content' => 'required|string',
        'rating' => 'required|integer|min:1|max:5',
        'date' => 'nullable|date',
    ]);

    Review::create([
        'product_id' => $request->product_id,
        'user_id' => $request->user_id,
        'content' => $request->content,
        'rating' => $request->rating,
        'created_at' => $request->date ? $request->date : now(),
    ]);

    UserDashboardController::clearHomepageCache();

    return redirect()->route('admin.reviews.index')->with('success', 'Review created successfully.');
}


public function edit($reviewId)
{
    $review = Review::with(['author', 'product.images'])->findOrFail($reviewId);
    // Eager-load images so the searchable picker can show a thumbnail per product.
    $products = Product::with('images')->orderBy('name')->get(['id', 'name']);
    // Include the review's current author even if their role isn't 'user'
    // (e.g. an admin-authored review) — otherwise the picker can't pre-select
    // them and the reviewer name disappears from the edit form.
    $users = User::where('role', 'user')
        ->orWhere('id', $review->user_id)
        ->orderBy('name')
        ->get(['id', 'name', 'email']);
    return view('admin.reviews.edit', compact('review', 'products', 'users'));
}


public function update(Request $request, $reviewId)
{
    // Validate the incoming data
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'product_id' => 'required|exists:products,id', // Validate product_id
        'content' => 'required|string',
        'rating' => 'required|integer|min:1|max:5',
        'date' => 'nullable|date',
    ]);

    // Find the review by its ID
    $review = Review::findOrFail($reviewId);

    // Update the review data
    $review->update([
        'user_id' => $request->user_id, // Update user_id
        'product_id' => $request->product_id, // Update product_id
        'content' => $request->content,
        'rating' => $request->rating,
        'created_at' => $request->date ? $request->date : $review->created_at,
    ]);

    UserDashboardController::clearHomepageCache();

    return redirect()->route('admin.reviews.index')->with('success', 'Review updated successfully.');
}


public function destroy($reviewId)
{
    // Find the review by its ID
    $review = Review::findOrFail($reviewId);

    // Delete the review
    $review->delete();

    UserDashboardController::clearHomepageCache();

    return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
}


}
