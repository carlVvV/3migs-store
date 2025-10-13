<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the cart contents.
     */
    public function index(): JsonResponse
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active && $product->in_stock) {
                $itemTotal = $item['quantity'] * $product->current_price;
                $total += $itemTotal;

                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->current_price,
                    'total_price' => $itemTotal,
                    'attributes' => $item['attributes'] ?? []
                ];
            } else {
                // Remove invalid items from cart
                unset($cart[$productId]);
            }
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'item_count' => count($cartItems),
                'total_quantity' => array_sum(array_column($cartItems, 'quantity'))
            ],
            'message' => 'Cart retrieved successfully'
        ]);
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'attributes' => 'sometimes|array'
        ]);

        $product = Product::find($request->product_id);

        if (!$product || !$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product not available'
            ], 400);
        }

        if (!$product->in_stock) {
            return response()->json([
                'success' => false,
                'message' => 'Product is out of stock'
            ], 400);
        }

        if ($product->manage_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $cart = Session::get('cart', []);
        $productId = $request->product_id;
        $quantity = $request->quantity;
        $attributes = $request->attributes ?? [];

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'quantity' => $quantity,
                'attributes' => $attributes
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => [
                'product' => $product,
                'quantity' => $cart[$productId]['quantity']
            ]
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = Session::get('cart', []);
        $productId = $request->product_id;
        $quantity = $request->quantity;

        if ($quantity == 0) {
            unset($cart[$productId]);
        } else {
            $product = Product::find($productId);
            
            if (!$product || !$product->is_active || !$product->in_stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not available'
                ], 400);
            }

            if ($product->manage_stock && $product->stock_quantity < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available'
                ], 400);
            }

            $cart[$productId]['quantity'] = $quantity;
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully'
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $cart = Session::get('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): JsonResponse
    {
        Session::forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Get cart summary (count and total).
     */
    public function summary(): JsonResponse
    {
        $cart = Session::get('cart', []);
        $itemCount = 0;
        $totalQuantity = 0;
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active && $product->in_stock) {
                $itemCount++;
                $totalQuantity += $item['quantity'];
                $total += $item['quantity'] * $product->current_price;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'item_count' => $itemCount,
                'total_quantity' => $totalQuantity,
                'total' => $total
            ],
            'message' => 'Cart summary retrieved successfully'
        ]);
    }
}
