<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomDesignOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomDesignOrderController extends Controller
{
    /**
     * Store a new custom design order.
     */
    public function store(Request $request)
    {
        Log::info('Custom design order creation started', [
            'user_id' => Auth::id(),
            'is_guest' => !Auth::check(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'fabric' => 'required|string',
            'color' => 'required|string',
            'embroidery' => 'nullable|string',
            'quantity' => 'required|integer|min:1|max:10',
            'measurements' => 'required|array',
            'measurements.chest' => 'required|numeric|min:20|max:60',
            'measurements.waist' => 'required|numeric|min:20|max:60',
            'measurements.length' => 'required|numeric|min:20|max:40',
            'measurements.shoulder_width' => 'required|numeric|min:12|max:25',
            'measurements.sleeve_length' => 'required|numeric|min:15|max:35',
            'fabric_yardage' => 'required|numeric|min:0.5|max:25',
            'reference_image' => 'nullable|file|image|max:10240', // 10MB max
            'pricing' => 'required|array',
            'additional_notes' => 'nullable|string|max:1000',
            'billing_address' => 'nullable|array', // Optional for guest users
            'billing_address.full_name' => 'nullable|string|max:255',
            'billing_address.street_address' => 'nullable|string|max:500',
            'billing_address.city' => 'nullable|string|max:100',
            'billing_address.province' => 'nullable|string|max:100',
            'billing_address.postal_code' => 'nullable|string|max:20',
            'billing_address.phone' => 'nullable|string|max:20',
            'billing_address.email' => 'nullable|email|max:255',
            'payment_method' => 'nullable|string|in:ewallet,cod', // Optional for initial creation
        ]);

        Log::info('Custom design order validation passed');

        try {
            DB::beginTransaction();
            Log::info('Database transaction started');

            // Handle reference image upload
            $referenceImagePath = null;
            if ($request->hasFile('reference_image')) {
                $file = $request->file('reference_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('custom-designs', $filename, 'public');
                $referenceImagePath = $path;
            }

            // Generate order number
            $orderNumber = CustomDesignOrder::generateOrderNumber();
            Log::info('Generated order number', ['order_number' => $orderNumber]);

            // Create custom design order
            $customOrder = CustomDesignOrder::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(), // Will be null for guest users
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method ?? 'ewallet',
                'total_amount' => $request->pricing['total'] ?? 0, // Use 'total' instead of 'totalCost'
                'currency' => 'PHP',
                'fabric' => $request->fabric,
                'color' => $request->color,
                'embroidery' => $request->embroidery ?? 'none',
                'quantity' => $request->quantity,
                'measurements' => $request->measurements,
                'fabric_yardage' => $request->fabric_yardage,
                'reference_image' => $referenceImagePath,
                'pricing' => $request->pricing,
                'additional_notes' => $request->additional_notes,
                'billing_address' => $request->billing_address ?? [], // Default to empty array for guests
                'shipping_address' => $request->billing_address ?? [], // Default to empty array for guests
                'estimated_completion_date' => now()->addDays(14), // 2 weeks estimated
            ]);

            Log::info('Custom design order created successfully', [
                'order_id' => $customOrder->id,
                'order_number' => $orderNumber,
                'total_amount' => $customOrder->total_amount
            ]);

            DB::commit();
            Log::info('Database transaction committed');

            return response()->json([
                'success' => true,
                'message' => 'Custom design order created successfully',
                'data' => [
                    'order' => $customOrder,
                    'order_number' => $orderNumber,
                    'total_amount' => $customOrder->total_amount,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Custom design order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create custom design order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's custom design orders.
     */
    public function index(Request $request)
    {
        Log::info('Fetching custom design orders for user', ['user_id' => Auth::id()]);
        
        $user = Auth::user();
        
        if (!$user) {
            Log::warning('Unauthenticated user attempted to access custom design orders');
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $orders = CustomDesignOrder::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        Log::info('Custom design orders fetched successfully', [
            'user_id' => $user->id,
            'orders_count' => $orders->count()
        ]);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get a specific custom design order.
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $order = CustomDesignOrder::forUser($user->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Update the specified custom design order.
     */
    public function update(Request $request, $id)
    {
        Log::info('Custom design order update started', [
            'order_id' => $id,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        try {
            $customOrder = CustomDesignOrder::findOrFail($id);
            
            // Check if user owns this order
            if ($customOrder->user_id !== Auth::id()) {
                Log::warning('Unauthorized access attempt to custom design order', [
                    'order_id' => $id,
                    'user_id' => Auth::id(),
                    'order_owner_id' => $customOrder->user_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'billing_address' => 'required|array',
                'billing_address.full_name' => 'required|string|max:255',
                'billing_address.street_address' => 'required|string|max:255',
                'billing_address.city' => 'required|string|max:100',
                'billing_address.province' => 'required|string|max:100',
                'billing_address.postal_code' => 'required|string|max:20',
                'billing_address.phone' => 'required|string|max:20',
                'billing_address.email' => 'required|email|max:255',
                'payment_method' => 'required|string|in:ewallet,cod'
            ]);

            if ($validator->fails()) {
                Log::warning('Custom design order update validation failed', [
                    'order_id' => $id,
                    'errors' => $validator->errors()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('Custom design order update validation passed');

            DB::beginTransaction();
            Log::info('Database transaction started for custom design order update');

            $customOrder->update([
                'billing_address' => $request->billing_address,
                'payment_method' => $request->payment_method,
                'status' => 'pending'
            ]);

            Log::info('Custom design order updated successfully', [
                'order_id' => $customOrder->id,
                'order_number' => $customOrder->order_number,
                'payment_method' => $customOrder->payment_method
            ]);

            DB::commit();
            Log::info('Database transaction committed for custom design order update');

            return response()->json([
                'success' => true,
                'message' => 'Custom design order updated successfully',
                'data' => [
                    'order' => $customOrder->fresh()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Custom design order update failed', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update custom design order'
            ], 500);
        }
    }

    /**
     * Update custom design order status (admin only).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled',
            'payment_status' => 'nullable|string|in:pending,paid,failed,refunded',
            'admin_notes' => 'nullable|string|max:1000',
            'estimated_completion_date' => 'nullable|date',
        ]);

        try {
            $order = CustomDesignOrder::findOrFail($id);
            
            $updateData = [
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
            ];

            if ($request->has('payment_status')) {
                $updateData['payment_status'] = $request->payment_status;
                
                if ($request->payment_status === 'paid' && !$order->paid_at) {
                    $updateData['paid_at'] = now();
                }
            }

            if ($request->has('estimated_completion_date')) {
                $updateData['estimated_completion_date'] = $request->estimated_completion_date;
            }

            $order->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all custom design orders (admin only).
     */
    public function adminIndex(Request $request)
    {
        $query = CustomDesignOrder::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}