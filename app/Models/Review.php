<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'review_text',
        'is_verified_purchase',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the user that owns the review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that owns the review
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(BarongProduct::class, 'product_id');
    }

    /**
     * Get the order that this review is for
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for verified purchases
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for high ratings
     */
    public function scopeHighRating($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Scope for low ratings
     */
    public function scopeLowRating($query)
    {
        return $query->where('rating', '<=', 2);
    }

    /**
     * Get average rating for a product
     */
    public static function getAverageRating($productId)
    {
        return self::where('product_id', $productId)
            ->where('is_approved', true)
            ->avg('rating');
    }

    /**
     * Get rating count for a product
     */
    public static function getRatingCount($productId)
    {
        return self::where('product_id', $productId)
            ->where('is_approved', true)
            ->count();
    }

    /**
     * Get rating distribution for a product
     */
    public static function getRatingDistribution($productId)
    {
        return self::where('product_id', $productId)
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();
    }

    /**
     * Check if user can review this product from this order
     */
    public static function canUserReview($userId, $productId, $orderId)
    {
        // Check if user already reviewed this product for this order
        $existingReview = self::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('order_id', $orderId)
            ->exists();

        if ($existingReview) {
            return false;
        }

        // Check if the order belongs to the user and contains the product
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('status', 'delivered') // Only allow reviews for delivered orders
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        return $order;
    }

    /**
     * Get reviews for a product with pagination
     */
    public static function getProductReviews($productId, $limit = 10)
    {
        return self::where('product_id', $productId)
            ->where('is_approved', true)
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }
}