<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\BarongProduct;
use Illuminate\Support\Facades\DB;

class CartSyncMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only sync if user is authenticated and has session cart
        if (Auth::check() && session()->has('cart')) {
            $this->syncCartWithDatabase(Auth::user());
        }

        return $next($request);
    }

    /**
     * Sync session cart with database cart
     */
    private function syncCartWithDatabase($user)
    {
        $sessionCart = session()->get('cart', []);
        
        if (empty($sessionCart)) {
            return;
        }

        try {
            DB::beginTransaction();

            foreach ($sessionCart as $productId => $details) {
                $product = BarongProduct::find($productId);
                
                if ($product && $product->is_available) {
                    $existingCartItem = Cart::where('user_id', $user->id)
                        ->where('product_id', $productId)
                        ->first();

                    if ($existingCartItem) {
                        // Update quantity if session has more
                        if ($details['quantity'] > $existingCartItem->quantity) {
                            $existingCartItem->update([
                                'quantity' => min($details['quantity'], $product->total_stock),
                                'price' => $product->current_price
                            ]);
                        }
                    } else {
                        // Create new cart item
                        Cart::create([
                            'user_id' => $user->id,
                            'product_id' => $productId,
                            'quantity' => min($details['quantity'], $product->total_stock),
                            'price' => $product->current_price
                        ]);
                    }
                }
            }

            // Clear session cart after sync
            session()->forget('cart');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error but don't break the request
            \Log::error('Cart sync failed: ' . $e->getMessage());
        }
    }
}