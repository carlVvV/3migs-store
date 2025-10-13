<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist
     */
    public function index()
    {
        $wishlist = Wishlist::getUserWishlist(Auth::id());
        
        return response()->json([
            'success' => true,
            'wishlist' => $wishlist
        ]);
    }

    /**
     * Add product to wishlist
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Check if already in wishlist
        if (Wishlist::isInWishlist(Auth::id(), $request->product_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist'
            ], 400);
        }

        Wishlist::addToWishlist(Auth::id(), $request->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully'
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $removed = Wishlist::removeFromWishlist(Auth::id(), $request->product_id);

        if ($removed) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in wishlist'
        ], 404);
    }

    /**
     * Toggle product in wishlist
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $productId = $request->product_id;
        $userId = Auth::id();

        if (Wishlist::isInWishlist($userId, $productId)) {
            Wishlist::removeFromWishlist($userId, $productId);
            $action = 'removed';
        } else {
            Wishlist::addToWishlist($userId, $productId);
            $action = 'added';
        }

        return response()->json([
            'success' => true,
            'message' => "Product {$action} from wishlist successfully",
            'action' => $action,
            'in_wishlist' => Wishlist::isInWishlist($userId, $productId)
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $inWishlist = Wishlist::isInWishlist(Auth::id(), $request->product_id);

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist
        ]);
    }

    /**
     * Clear entire wishlist
     */
    public function clear()
    {
        Wishlist::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared successfully'
        ]);
    }

    /**
     * Get wishlist count
     */
    public function count()
    {
        $count = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}