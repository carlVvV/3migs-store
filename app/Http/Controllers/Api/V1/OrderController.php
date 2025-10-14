<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BarongProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\BuxPostbackService;
use App\Services\BuxService;

class OrderController extends Controller
{
    /**
     * Get user orders
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $status = $request->get('status');
            
            $query = Auth::user()->orders()->with(['orderItems.product']);
            
            if ($status) {
                $query->where('status', $status);
            }
            
            $orders = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific order details
     */
    public function show($id)
    {
        try {
            // Use session-based auth instead of Auth::user() for public routes
            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            $order = Order::where('user_id', $userId)
                ->with(['user', 'orderItems.product'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel($id)
    {
        try {
            $order = Auth::user()->orders()->findOrFail($id);

            if ($order->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already cancelled'
                ], 400);
            }

            if (in_array($order->status, ['shipped', 'delivered'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel order that has been shipped or delivered'
                ], 400);
            }

            DB::beginTransaction();

            // Update order status
            $order->update(['status' => 'cancelled']);

            // Restore product stock
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                if ($product->has_variations && !empty($product->variations)) {
                    // For products with variations, we need to restore variation stock
                    $variations = $product->variations;
                    // For simplicity, we'll restore to the first variation
                    if (!empty($variations)) {
                        $variations[0]['stock'] += $item->quantity;
                        $product->variations = $variations;
                        $product->save();
                    }
                } else {
                    $product->increment('stock', $item->quantity);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seed a sample order for the currently authenticated user (for testing UI)
     */
    public function seedSample(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            // Pick a product to add; if none, create a simple placeholder product
            $product = BarongProduct::query()->first();
            if (!$product) {
                $product = BarongProduct::create([
                    'name' => 'Sample Barong',
                    'slug' => 'sample-barong-'.strtolower(str()->random(6)),
                    'description' => 'Auto-generated sample product for testing orders',
                    'type' => 'Traditional Barong',
                    'brand_id' => null,
                    'category_id' => null,
                    'images' => [],
                    'base_price' => 2000,
                    'stock' => 10,
                    'is_available' => true,
                    'is_featured' => false,
                    'has_variations' => false,
                    'sku' => 'SAMPLE-'.strtoupper(str()->random(5)),
                ]);
            }

            // Create order
            $order = new Order();
            $order->user_id = $user->id;
            $order->order_number = 'ORD-'.now()->format('YmdHis').'-'.strtoupper(str()->random(4));
            $order->status = 'pending';
            $order->payment_method = 'cod';
            $order->shipping_address = json_encode([
                'full_name' => $user->name,
                'company_name' => null,
                'street_address' => '123 Test Street',
                'apartment' => null,
                'city' => 'Pandi',
                'province' => 'Bulacan',
                'postal_code' => '3014',
                'phone' => $user->phone ?? '09123456789',
                'email' => $user->email,
            ]);
            $order->billing_address = $order->shipping_address;
            $order->subtotal = 0; // required not-null columns
            $order->shipping_fee = 0;
            $order->discount = 0;
            $order->total_amount = 0; // set after items
            $order->save();

            // Create one or two items
            $quantity = 1;
            $unitPrice = (float) ($product->base_price ?? 2000);
            $total = $unitPrice * $quantity;

            $item = new OrderItem();
            $item->order_id = $order->id;
            $item->product_id = $product->id;
            $item->quantity = $quantity;
            $item->unit_price = $unitPrice;
            $item->total_price = $total;
            $item->save();

            // Update order totals
            $order->subtotal = $total;
            // Use only columns that exist: subtotal, shipping_fee, discount, total_amount
            $order->total_amount = $total + (float) $order->shipping_fee - (float) $order->discount;
            $order->save();

            $order->load(['user','orderItems.product']);

            return response()->json([
                'success' => true,
                'message' => 'Sample order created',
                'data' => $order
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to seed order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics for user
     */
    public function statistics()
    {
        try {
            $user = Auth::user();
            
            $stats = [
                'total_orders' => $user->orders()->count(),
                'pending_orders' => $user->orders()->where('status', 'pending')->count(),
                'processing_orders' => $user->orders()->where('status', 'processing')->count(),
                'completed_orders' => $user->orders()->where('status', 'completed')->count(),
                'cancelled_orders' => $user->orders()->where('status', 'cancelled')->count(),
                'total_spent' => $user->orders()->where('status', 'completed')->sum('total_amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:500',
            'apartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'save_info' => 'boolean',
            'payment_method' => 'required|in:ewallet,cod'
        ]);

        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to place an order'
                ], 401);
            }

            // Get cart items
            $cartItems = $user->cart()->with('product')->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            // Apply coupon discount if any
            $discount = 0;
            $appliedCoupon = session('applied_coupon');
            if ($appliedCoupon && isset($appliedCoupon['discount'])) {
                $discount = $subtotal * $appliedCoupon['discount'];
            }

            $total = $subtotal - $discount;

            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . time() . '-' . rand(1000, 9999),
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => 0, // Free shipping
                'total_amount' => $total,
                'billing_address' => [
                    'full_name' => $request->full_name,
                    'company_name' => $request->company_name,
                    'street_address' => $request->street_address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'province' => $request->province,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                    'email' => $request->email,
                ],
                'shipping_address' => [
                    'full_name' => $request->full_name,
                    'company_name' => $request->company_name,
                    'street_address' => $request->street_address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'province' => $request->province,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                    'email' => $request->email,
                ]
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price,
                    'total_price' => $cartItem->price * $cartItem->quantity
                ]);

                // Update product stock
                $product = $cartItem->product;
                if ($product->has_variations && !empty($product->variations)) {
                    // For products with variations, reduce variation stock
                    $variations = $product->variations;
                    if (!empty($variations)) {
                        $variations[0]['stock'] = max(0, $variations[0]['stock'] - $cartItem->quantity);
                        $product->variations = $variations;
                        $product->save();
                    }
                } else {
                    $product->decrement('stock', $cartItem->quantity);
                }
            }

            // Clear user's cart
            $user->cart()->delete();

            // Clear applied coupon
            session()->forget('applied_coupon');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order' => $order,
                    'order_number' => $order->order_number
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Bux.ph checkout URL for an order (authenticated user for own order).
     */
    public function buxCheckout(Request $request, $id, BuxService $bux)
    {
        $order = Order::with(['orderItems', 'user'])->findOrFail($id);

        if (Auth::id() !== $order->user_id) {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        $shipping = is_string($order->shipping_address) ? (json_decode($order->shipping_address, true) ?: []) : ($order->shipping_address ?? []);
        $billing = is_string($order->billing_address) ? (json_decode($order->billing_address, true) ?: []) : ($order->billing_address ?? []);

        // Match the JavaScript payload structure exactly
        $payload = [
            'req_id' => $order->order_number,
            'amount' => (float) $order->total_amount,
            'description' => 'Order #'.$order->order_number,
            'email' => $order->user->email ?? ($shipping['email'] ?? null),
            'expiry' => 2, // 2 hours expiry
            'notification_url' => url('/api/v1/payments/bux/webhook'),
            'redirect_url' => url('/orders'),
            'name' => $order->user->name ?? ($shipping['full_name'] ?? null),
            'contact' => $shipping['phone'] ?? $billing['phone'] ?? null,
            'param1' => $order->order_number,
        ];

        $result = $bux->generateCheckoutUrl($payload);
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create checkout',
                'error' => $result['error'] ?? 'Unknown error',
            ], 400);
        }

        // Get the checkout URL from Bux response and redirect URL from service
        $checkoutData = $result['data'];
        $redirectUrl = $bux->getRedirectUrl();

        return response()->json([
            'success' => true,
            'data' => [
                'checkout_url' => $checkoutData['checkout_url'] ?? $redirectUrl,
                'redirect_url' => $redirectUrl,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);
    }

    /**
     * Enhanced webhook for Bux.ph payment notifications (Postback)
     */
    public function buxWebhook(Request $request, BuxPostbackService $postbackService)
    {
        try {
            // Log incoming webhook for debugging
            Log::info('Bux.ph webhook received', [
                'headers' => $request->headers->all(),
                'payload' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Process the postback
            $result = $postbackService->processPostback($request->all());

            if ($result['success']) {
                Log::info('Bux.ph webhook processed successfully', [
                    'order_number' => $result['order_number'] ?? 'unknown',
                    'message' => $result['message']
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ], 200);
            } else {
                Log::error('Bux.ph webhook processing failed', [
                    'error' => $result['message'],
                    'payload' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Bux.ph webhook exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Test webhook endpoint (for development)
     */
    public function testBuxWebhook(Request $request, BuxPostbackService $postbackService)
    {
        $orderNumber = $request->input('order_number');
        $status = $request->input('status', 'paid');

        if (!$orderNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Order number is required'
            ], 400);
        }

        $result = $postbackService->testPostback($orderNumber, $status);

        return response()->json($result);
    }
}