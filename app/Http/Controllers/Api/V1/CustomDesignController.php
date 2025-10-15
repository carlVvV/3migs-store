<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CustomDesignController extends Controller
{
    /**
     * Store custom barong order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fabric' => 'required|string|in:pina,jusi,cotton,linen,silk',
            'color' => 'required|string|in:white,cream,ivory,beige,light-blue,light-pink',
            'embroidery' => 'nullable|string|in:none,simple,detailed,custom',
            'quantity' => 'required|integer|min:1|max:10',
            'measurements' => 'required|array',
            'measurements.chest' => 'required|numeric|min:20|max:60',
            'measurements.waist' => 'required|numeric|min:20|max:60',
            'measurements.length' => 'required|numeric|min:20|max:40',
            'measurements.shoulder_width' => 'required|numeric|min:12|max:25',
            'measurements.sleeve_length' => 'required|numeric|min:15|max:35',
            'additional_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Calculate pricing
            $basePrices = [
                'pina' => 2500,
                'jusi' => 2000,
                'cotton' => 1500,
                'linen' => 1800,
                'silk' => 3000
            ];

            $embroideryPrices = [
                'none' => 0,
                'simple' => 200,
                'detailed' => 500,
                'custom' => 1000
            ];

            $basePrice = $basePrices[$request->fabric];
            $embroideryPrice = $embroideryPrices[$request->embroidery] ?? 0;
            $unitPrice = $basePrice + $embroideryPrice;
            $totalPrice = $unitPrice * $request->quantity;

            // Create custom product entry
            $customProduct = Product::create([
                'name' => 'Custom Barong - ' . ucfirst($request->fabric),
                'slug' => 'custom-barong-' . $request->fabric . '-' . time(),
                'description' => "Custom barong made with {$request->fabric} fabric in {$request->color} color. Custom measurements provided. Embroidery: {$request->embroidery}",
                'price' => $unitPrice,
                'sale_price' => null,
                'sku' => 'CUSTOM-' . strtoupper($request->fabric) . '-' . time(),
                'stock_quantity' => 0, // Custom items are made to order
                'manage_stock' => false,
                'is_featured' => false,
                'is_active' => true,
                'category_id' => 1, // Assuming barong category ID is 1
                'meta_title' => 'Custom Barong Order',
                'meta_description' => 'Custom barong made to your specifications',
                'custom_options' => json_encode([
                    'fabric' => $request->fabric,
                    'color' => $request->color,
                    'embroidery' => $request->embroidery,
                    'measurements' => $request->measurements,
                    'additional_notes' => $request->additional_notes,
                    'is_custom' => true
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom barong order created successfully',
                'data' => [
                    'product_id' => $customProduct->id,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'custom_options' => json_decode($customProduct->custom_options)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create custom order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add custom barong to cart
     */
    public function addToCart(Request $request)
    {
        // Normalize/derive inputs to avoid common validation failures coming from the UI
        $payload = $request->all();

        // If yardage wasn't explicitly sent, try to derive it from pricing.yardage
        if ((!isset($payload['fabric_yardage']) || $payload['fabric_yardage'] === '' || $payload['fabric_yardage'] === null)
            && isset($payload['pricing']['yardage'])) {
            $payload['fabric_yardage'] = (float) $payload['pricing']['yardage'];
        }

        // Coerce measurement values to numbers when they come as strings
        if (isset($payload['measurements']) && is_array($payload['measurements'])) {
            foreach (['chest','waist','length','shoulder_width','sleeve_length'] as $k) {
                if (isset($payload['measurements'][$k])) {
                    $payload['measurements'][$k] = (float) $payload['measurements'][$k];
                }
            }
        }

        // Recreate request with normalized payload for validation
        $request->replace($payload);

        $validator = Validator::make($request->all(), [
            'fabric' => 'required|string|in:pina,jusi,cotton,linen,silk,jusilyn,hugo_boss,pina_cocoon,gusot_mayaman',
            'color' => 'required|string|in:white,cream,ivory,beige,light-blue,light-pink',
            'embroidery' => 'nullable|string|in:none,simple,detailed,custom',
            'quantity' => 'required|integer|min:1|max:10',
            'measurements' => 'required|array',
            'measurements.chest' => 'required|numeric|min:20|max:60',
            'measurements.waist' => 'required|numeric|min:20|max:60',
            'measurements.length' => 'required|numeric|min:20|max:40',
            'measurements.shoulder_width' => 'required|numeric|min:12|max:25',
            'measurements.sleeve_length' => 'required|numeric|min:15|max:35',
            // Allow slightly wider range to accommodate larger sizes
            'fabric_yardage' => 'required|numeric|min:0.5|max:25',
            'reference_image' => 'nullable|file|image|max:10240', // 10MB max
            'pricing' => 'required|array',
            'additional_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Use pricing from frontend calculation (ensure array)
            $pricing = is_array($request->pricing) ? $request->pricing : [];
            $unitPrice = $pricing['totalCost'] ?? 2000; // Fallback price
            
            // Note: Custom embroidery pricing varies based on design complexity
            // Base price is ₱500, but can range from ₱500-₱2000+ depending on design

            // Create custom product data for cart
            $customProductData = [
                'id' => 'custom_' . time(),
                'name' => 'Custom Barong - ' . ucfirst(str_replace('_', ' ', $request->fabric)),
                'price' => $unitPrice,
                'quantity' => $request->quantity,
                'image' => '/images/custom-barong-placeholder.jpg',
                'custom_options' => [
                    'fabric' => $request->fabric,
                    'color' => $request->color,
                    'embroidery' => $request->embroidery,
                    'measurements' => $request->measurements,
                    'fabric_yardage' => (float) $request->fabric_yardage,
                    'pricing' => $pricing,
                    'additional_notes' => $request->additional_notes,
                    'is_custom' => true
                ]
            ];

            // Add to session cart (for guest users) or user cart
            if (auth()->check()) {
                // Add to authenticated user's cart
                // This would integrate with your existing cart system
                return response()->json([
                    'success' => true,
                    'message' => 'Custom barong added to cart',
                    'data' => $customProductData
                ]);
            } else {
                // Add to session cart
                $cart = session()->get('cart', []);
                $cartKey = 'custom_' . $request->fabric . '_' . $request->color . '_' . md5(json_encode($request->measurements));
                
                if (isset($cart[$cartKey])) {
                    $cart[$cartKey]['quantity'] += $request->quantity;
                } else {
                    $cart[$cartKey] = $customProductData;
                }
                
                session()->put('cart', $cart);

                return response()->json([
                    'success' => true,
                    'message' => 'Custom barong added to cart',
                    'data' => $customProductData
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add custom barong to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get custom barong pricing
     */
    public function getPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fabric' => 'required|string|in:pina,jusi,cotton,linen,silk',
            'embroidery' => 'nullable|string|in:none,simple,detailed,custom'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $basePrices = [
            'pina' => 2500,
            'jusi' => 2000,
            'cotton' => 1500,
            'linen' => 1800,
            'silk' => 3000
        ];

        $embroideryPrices = [
            'none' => 0,
            'simple' => 200,
            'detailed' => 500,
            'custom' => 1000
        ];

        $basePrice = $basePrices[$request->fabric];
        $embroideryPrice = $embroideryPrices[$request->embroidery] ?? 0;
        $totalPrice = $basePrice + $embroideryPrice;

        return response()->json([
            'success' => true,
            'data' => [
                'base_price' => $basePrice,
                'embroidery_price' => $embroideryPrice,
                'total_price' => $totalPrice,
                'fabric_name' => ucfirst($request->fabric),
                'embroidery_name' => ucfirst($request->embroidery ?? 'none')
            ]
        ]);
    }
}
