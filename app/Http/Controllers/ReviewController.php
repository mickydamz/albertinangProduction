<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Review;
use App\Models\Product;
use App\Models\Order;

class ReviewController extends Controller
{
    /**
     * Store a newly created review.
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|between:1,5',
            'comment'    => 'required|string|min:10|max:1000',
        ]);

        // Validate product ID matches route
        if ($request->product_id != $productId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product ID.',
            ], 400);
        }

        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to submit a review.',
            ], 401);
        }

        // ═══════════════════════════════════════════════════════
        // CHECK IF USER HAS PURCHASED THIS PRODUCT
        // ═══════════════════════════════════════════════════════
        $hasPurchased = Order::where('user_id', auth()->id())
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed', 'delivered'])
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review products you have purchased. Please purchase this product first.',
            ], 403);
        }

        // ═══════════════════════════════════════════════════════
        // CHECK IF USER ALREADY REVIEWED THIS PRODUCT
        // ═══════════════════════════════════════════════════════
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product.',
            ], 422);
        }

        try {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            // Create the review. The form field is named "comment"; it maps to the
            // reviews.content column (the single source of truth for review text).
            $review = Review::create([
                'product_id' => $request->product_id,
                'user_id'    => auth()->id(),
                'rating'     => $request->rating,
                'content'    => $request->comment,
            ]);

            // Recalculate and update product's average rating
            $this->updateProductRating($product);

            return response()->json([
                'success'        => true,
                'message'        => 'Review submitted successfully!',
                'user_name'      => $review->user_name,
                'rating'         => $review->rating,
                'content'        => $review->content,
                'average_rating' => round($product->fresh()->rating, 1),
                'rating_count'   => $product->fresh()->rating_count,
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Review submission failed: ' . $e->getMessage(), [
                'product_id'  => $request->product_id,
                'user_id'     => auth()->id(),
                'rating'      => $request->rating,
                'comment'     => $request->comment,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review. Please try again.',
            ], 500);
        }
    }

    /**
     * Update an existing review.
     */
    public function update(Request $request, $reviewId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'rating'  => 'required|integer|between:1,5',
        ]);

        try {
            $review = Review::findOrFail($reviewId);

            // Check authorization: only admin or the review owner can update
            if (auth()->user()->role !== 'admin' && auth()->id() !== $review->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action. You can only edit your own reviews.',
                ], 403);
            }

            $review->update([
                'content' => $request->comment,
                'rating'  => $request->rating,
            ]);

            // Recalculate product rating after update
            $product = $review->product;
            $this->updateProductRating($product);

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully.',
                'review'  => [
                    'id'         => $review->id,
                    'rating'     => $review->rating,
                    'content'    => $review->content,
                    'user_name'  => $review->user_name,
                    'updated_at' => $review->updated_at->diffForHumans(),
                ],
                'average_rating' => round($product->fresh()->rating, 1),
                'rating_count'   => $product->fresh()->rating_count,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Review update failed: ' . $e->getMessage(), [
                'review_id'   => $reviewId,
                'user_id'     => auth()->id(),
                'rating'      => $request->rating ?? null,
                'comment'     => $request->comment ?? null,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review. Please try again.',
            ], 500);
        }
    }

    /**
     * Delete a review (Admin only or review owner).
     */
    public function destroy($reviewId)
    {
        try {
            $review = Review::findOrFail($reviewId);

            // Check authorization
            if (auth()->user()->role !== 'admin' && auth()->id() !== $review->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.',
                ], 403);
            }

            $product = $review->product;
            $review->delete();

            // Recalculate product rating after deletion
            $this->updateProductRating($product);

            return response()->json([
                'success'        => true,
                'message'        => 'Review deleted successfully.',
                'average_rating' => round($product->fresh()->rating, 1),
                'rating_count'   => $product->fresh()->rating_count,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Review deletion failed: ' . $e->getMessage(), [
                'review_id'   => $reviewId,
                'user_id'     => auth()->id(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review.',
            ], 500);
        }
    }

    /**
     * Get reviews for a specific product.
     */
    public function getProductReviews($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $reviews = $product->reviews()
                ->with('user:id,name')
                ->latest()
                ->paginate(10);

            $ratingDistribution = [];
            foreach ([5, 4, 3, 2, 1] as $star) {
                $ratingDistribution[$star] = $product->reviews()->where('rating', $star)->count();
            }

            return response()->json([
                'success'             => true,
                'reviews'             => $reviews,
                'average_rating'      => round($product->rating, 1),
                'rating_count'        => $product->rating_count,
                'rating_distribution' => $ratingDistribution,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load reviews.',
            ], 500);
        }
    }

    /**
     * Get reviews by the authenticated user.
     */
    public function getUserReviews()
    {
        try {
            $reviews = Review::where('user_id', auth()->id())
                ->with('product:id,name,images')
                ->latest()
                ->paginate(10);

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load your reviews.',
            ], 500);
        }
    }

    /**
     * Check if the authenticated user can review a product.
     */
    public function canReview($productId)
    {
        if (!auth()->check()) {
            return response()->json([
                'can_review' => false,
                'reason'     => 'You must be logged in to review.',
            ]);
        }

        // Check if already reviewed
        $alreadyReviewed = Review::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'can_review'      => false,
                'reason'          => 'You have already reviewed this product.',
                'existing_review' => Review::where('user_id', auth()->id())
                    ->where('product_id', $productId)
                    ->first(),
            ]);
        }

        // Check if purchased
        $hasPurchased = Order::where('user_id', auth()->id())
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed', 'delivered'])
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'can_review' => false,
                'reason'     => 'You can only review products you have purchased.',
            ]);
        }

        return response()->json([
            'can_review' => true,
            'reason'     => 'You can review this product.',
        ]);
    }

    /**
     * Update product's average rating and rating count.
     */
    private function updateProductRating(Product $product): Product
    {
        $reviews = $product->reviews()->whereNotNull('rating')->get();

        $product->rating_count = $reviews->count();
        $product->rating       = $reviews->count() > 0
            ? round($reviews->avg('rating'), 1)
            : 0;

        $product->save();

        // The homepage caches its product collections (with rating aggregates)
        // for 15 min, so a new/changed/removed review would otherwise not show
        // there until the cache expired. Bust it so the home page matches the
        // live pages immediately.
        UserDashboardController::clearHomepageCache();

        return $product;
    }
}