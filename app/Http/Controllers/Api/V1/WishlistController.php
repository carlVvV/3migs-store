<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\BarongProduct;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Get user wishlist
     */
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        try {
            $wishlist = Wishlist::with('product.category')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();

            $items = $wishlist->filter(function ($item) {
                return !is_null($item->product);
            })->map(function ($item) {
                $product = $item->product;
                $imageUrl = '/images/placeholder.jpg';
                if ($product) {
                    // Prefer cover image URL accessor
                    $imageUrl = $product->cover_image_url ?? $imageUrl;
                    // Fallback to first image if available
                    if ($imageUrl === '/images/placeholder.jpg' && !empty($product->images) && is_array($product->images)) {
                        $imageUrl = $product->images[0] ?: $imageUrl;
                    }
                }
                return [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $imageUrl,
                    'current_price' => $product->current_price,
                    'original_price' => $product->is_on_sale ? $product->base_price : null,
                    'category' => optional($product->category)->name ?? 'Barong',
                    'is_available' => (bool) $product->is_available,
                    'total_stock' => $product->getTotalStock(),
                    'created_at' => $item->created_at
                ];
            })->values();

            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add product to wishlist
     */
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        $request->validate([
            'product_id' => 'required|exists:barong_products,id',
        ]);

        try {
            $product = BarongProduct::findOrFail($request->product_id);
            
            // Check if already in wishlist
            $existingWishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingWishlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in wishlist'
                ], 400);
            }

            $wishlist = Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
            ]);

            $wishlist->load('product');

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist',
                'data' => $wishlist
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove product from wishlist by product ID
     */
    public function removeByProduct(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        $request->validate([
            'product_id' => 'required|exists:barong_products,id',
        ]);

        try {
            $wishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->first();

            if (!$wishlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in wishlist'
                ], 404);
            }

            $wishlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove product from wishlist by wishlist item ID
     */
    public function remove($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        try {
            $wishlist = Wishlist::where('user_id', Auth::id())
                ->where('id', $id)
                ->first();

            if (!$wishlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wishlist item not found'
                ], 404);
            }

            $wishlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear entire wishlist
     */
    public function clear()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        try {
            Wishlist::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Wishlist cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
        $request->validate([
            'product_id' => 'required|exists:barong_products,id',
        ]);

        try {
            $isInWishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->exists();

            return response()->json([
                'success' => true,
                'data' => [
                    'is_in_wishlist' => $isInWishlist
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check wishlist status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wishlist count
     */
    public function count()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => true,
                    'data' => ['count' => 0],
                ]);
            }
            $count = Wishlist::where('user_id', Auth::id())->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'count' => $count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get wishlist count',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
