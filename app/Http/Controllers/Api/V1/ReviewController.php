<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\BarongProduct;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:barong_products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();
        $productId = $request->product_id;
        $orderId = $request->order_id;

        // Check if user can review this product
        if (!Review::canUserReview($userId, $productId, $orderId)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot review this product. Make sure you have purchased it and it has been delivered.'
            ], 403);
        }

        try {
            $review = Review::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $orderId,
                'rating' => $request->rating,
                'review_text' => $request->review_text,
                'is_verified_purchase' => true,
                'is_approved' => true, // Auto-approve for now
            ]);

            // Update product's average rating and review count
            $this->updateProductRatings($productId);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'data' => [
                    'review' => $review->load('user:id,name'),
                    'product_rating' => BarongProduct::find($productId)->average_rating,
                    'product_review_count' => BarongProduct::find($productId)->review_count,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reviews for a product
     */
    public function getProductReviews(Request $request, $productId): JsonResponse
    {
        $limit = $request->get('limit', 10);
        
        try {
            $reviews = Review::getProductReviews($productId, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rating distribution for a product
     */
    public function getRatingDistribution($productId): JsonResponse
    {
        try {
            $distribution = Review::getRatingDistribution($productId);
            
            return response()->json([
                'success' => true,
                'data' => $distribution
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rating distribution: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can review a product from an order
     */
    public function canReview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:barong_products,id',
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();
        $productId = $request->product_id;
        $orderId = $request->order_id;

        $canReview = Review::canUserReview($userId, $productId, $orderId);

        return response()->json([
            'success' => true,
            'data' => [
                'can_review' => $canReview,
                'reason' => $canReview ? 'You can review this product' : 'You cannot review this product'
            ]
        ]);
    }

    /**
     * Get user's reviews
     */
    public function getUserReviews(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $limit = $request->get('limit', 10);
            
            $reviews = Review::where('user_id', $userId)
                ->with(['product:id,name,slug,cover_image', 'order:id,order_number'])
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user reviews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product ratings after review submission
     */
    private function updateProductRatings($productId): void
    {
        $product = BarongProduct::find($productId);
        if ($product) {
            // The average_rating and review_count are calculated dynamically
            // through the model accessors, so no need to update database fields
            // unless you want to cache them for performance
        }
    }
}

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:barong_products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();
        $productId = $request->product_id;
        $orderId = $request->order_id;

        // Check if user can review this product
        if (!Review::canUserReview($userId, $productId, $orderId)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot review this product. Make sure you have purchased it and it has been delivered.'
            ], 403);
        }

        try {
            $review = Review::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $orderId,
                'rating' => $request->rating,
                'review_text' => $request->review_text,
                'is_verified_purchase' => true,
                'is_approved' => true, // Auto-approve for now
            ]);

            // Update product's average rating and review count
            $this->updateProductRatings($productId);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'data' => [
                    'review' => $review->load('user:id,name'),
                    'product_rating' => BarongProduct::find($productId)->average_rating,
                    'product_review_count' => BarongProduct::find($productId)->review_count,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reviews for a product
     */
    public function getProductReviews(Request $request, $productId): JsonResponse
    {
        $limit = $request->get('limit', 10);
        
        try {
            $reviews = Review::getProductReviews($productId, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rating distribution for a product
     */
    public function getRatingDistribution($productId): JsonResponse
    {
        try {
            $distribution = Review::getRatingDistribution($productId);
            
            return response()->json([
                'success' => true,
                'data' => $distribution
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rating distribution: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can review a product from an order
     */
    public function canReview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:barong_products,id',
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();
        $productId = $request->product_id;
        $orderId = $request->order_id;

        $canReview = Review::canUserReview($userId, $productId, $orderId);

        return response()->json([
            'success' => true,
            'data' => [
                'can_review' => $canReview,
                'reason' => $canReview ? 'You can review this product' : 'You cannot review this product'
            ]
        ]);
    }

    /**
     * Get user's reviews
     */
    public function getUserReviews(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $limit = $request->get('limit', 10);
            
            $reviews = Review::where('user_id', $userId)
                ->with(['product:id,name,slug,cover_image', 'order:id,order_number'])
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user reviews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product ratings after review submission
     */
    private function updateProductRatings($productId): void
    {
        $product = BarongProduct::find($productId);
        if ($product) {
            // The average_rating and review_count are calculated dynamically
            // through the model accessors, so no need to update database fields
            // unless you want to cache them for performance
        }
    }
}