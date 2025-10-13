<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use App\Models\Product;

class ReviewController extends Controller
{
    /**
     * Get reviews for a product
     */
    public function index(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $query = $product->reviews()->with('user');
        
        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }
        
        // Filter by verified purchases
        if ($request->has('verified') && $request->verified) {
            $query->where('is_verified_purchase', true);
        }
        
        // Sort by helpful count or date
        $sortBy = $request->get('sort', 'helpful');
        if ($sortBy === 'helpful') {
            $query->orderBy('helpful_count', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $reviews = $query->paginate(10);
        
        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'average_rating' => $product->average_rating,
                'review_count' => $product->review_count,
                'rating_distribution' => $product->rating_distribution,
            ]
        ]);
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);
        
        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product'
            ], 400);
        }

        // Check if user has purchased this product (for verified purchase)
        $hasPurchased = $product->orderItems()
            ->whereHas('order', function($query) {
                $query->where('user_id', Auth::id())
                      ->where('status', 'completed');
            })
            ->exists();

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_verified_purchase' => $hasPurchased,
            'images' => $request->images ? array_map(function($image) {
                return $image->store('reviews', 'public');
            }, $request->images) : null,
        ]);

        $review->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review' => $review
        ]);
    }

    /**
     * Update a review
     */
    public function update(Request $request, $reviewId)
    {
        $review = Review::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'images' => $request->images ? array_map(function($image) {
                return $image->store('reviews', 'public');
            }, $request->images) : $review->images,
        ]);

        $review->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'review' => $review
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy($reviewId)
    {
        $review = Review::where('id', $reviewId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        
        // In a real application, you'd want to track which users marked it helpful
        // to prevent duplicate votes. For now, we'll just increment.
        
        $review->increment('helpful_count');

        return response()->json([
            'success' => true,
            'message' => 'Review marked as helpful',
            'helpful_count' => $review->helpful_count
        ]);
    }

    /**
     * Get user's reviews
     */
    public function userReviews()
    {
        $reviews = Auth::user()->reviews()
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }
}