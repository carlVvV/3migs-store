<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarongProduct;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                // For guest users, try to get session cart
                $sessionCart = session()->get('cart', []);
                $items = [];
                $total = 0;
                $totalQuantity = 0;

                foreach ($sessionCart as $productId => $details) {
                    // Handle custom barong items
                    if (is_string($productId) && str_starts_with($productId, 'custom_')) {
                        $subtotal = ($details['price'] ?? 0) * ($details['quantity'] ?? 1);
                        $total += $subtotal;
                        $totalQuantity += $details['quantity'] ?? 1;

                        $items[] = [
                            'id' => $productId,
                            'product_id' => null, // Custom items don't have a product_id
                            'name' => $details['name'] ?? 'Custom Barong',
                            'slug' => null,
                            'price' => $details['price'] ?? 0,
                            'current_price' => $details['price'] ?? 0,
                            'special_price' => null,
                            'is_on_sale' => false,
                            'discount_percentage' => 0,
                            'quantity' => $details['quantity'] ?? 1,
                            'subtotal' => $subtotal,
                            'category' => null,
                            'images' => [$details['image'] ?? '/images/custom-barong-placeholder.jpg'],
                            'stock_quantity' => 999, // Custom items have unlimited stock
                            'added_at' => $details['added_at'] ?? now(),
                            'is_custom' => true,
                            'custom_options' => $details['custom_options'] ?? [],
                        ];
                        continue;
                    }

                    $product = BarongProduct::with('category')->find($productId);
                    
                    if ($product && $product->is_available) {
                        $subtotal = $product->current_price * $details['quantity'];
                        $total += $subtotal;
                        $totalQuantity += $details['quantity'];

                        $items[] = [
                            'id' => $product->id, // Use product ID for session cart
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'slug' => $product->slug,
                            'price' => $product->current_price,
                            'current_price' => $product->current_price,
                            'special_price' => $product->special_price,
                            'is_on_sale' => $product->is_on_sale,
                            'discount_percentage' => $product->discount_percentage,
                            'quantity' => $details['quantity'],
                            'subtotal' => $subtotal,
                            'category' => $product->category,
                            'images' => $product->images,
                            'stock_quantity' => $product->getTotalStock(),
                            'added_at' => $details['added_at'] ?? now(),
                            'size' => $details['size'] ?? null,
                            'color' => $details['color'] ?? null,
                        ];
                    }
                }

                return response()->json([
                    'success' => true,
                    'items' => $items,
                    'subtotal' => $total,
                    'total' => $total,
                    'is_guest' => true
                ]);
            }

            $cartItems = Cart::with(['product.category'])
                ->where('user_id', $user->id)
                ->active()
                ->get();

            $items = [];
            $total = 0;
            $totalQuantity = 0;

            $totalWholesaleSavings = 0;

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                $subtotal = $cartItem->price * $cartItem->quantity;
                $total += $subtotal;
                $totalQuantity += $cartItem->quantity;

                // Calculate wholesale savings for this item
                $regularPrice = (float) $product->current_price;
                $wholesalePrice = $cartItem->price < $regularPrice ? (float) $cartItem->price : null;
                $wholesaleSavings = $wholesalePrice ? ($regularPrice - $wholesalePrice) * $cartItem->quantity : 0;
                $totalWholesaleSavings += $wholesaleSavings;

                $items[] = [
                    'id' => $cartItem->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $cartItem->price, // Price when added to cart (may be wholesale)
                    'current_price' => $product->current_price, // Current regular price
                    'special_price' => $product->special_price,
                    'is_on_sale' => $product->is_on_sale,
                    'discount_percentage' => $product->discount_percentage,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $subtotal,
                    'category' => $product->category,
                    'images' => $product->images,
                    'stock_quantity' => $product->getTotalStock(),
                    'added_at' => $cartItem->created_at,
                    'is_wholesale_price' => $wholesalePrice !== null,
                    'wholesale_price' => $wholesalePrice,
                    'wholesale_savings' => $wholesaleSavings,
                    'size' => $cartItem->size ?? null,
                    'color' => $cartItem->color ?? null,
                ];
            }

            return response()->json([
                'success' => true,
                'items' => $items,
                'subtotal' => $total,
                'total' => $total,
                'wholesale_savings' => $totalWholesaleSavings,
                'is_guest' => false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        // Enhanced logging for debugging 422 errors
        \Log::info('=== CART ADD REQUEST START ===', [
            'timestamp' => now()->toISOString(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_headers' => $request->headers->all(),
            'raw_input' => $request->getContent(),
            'parsed_input' => $request->all(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'referer' => $request->header('Referer'),
            'content_type' => $request->header('Content-Type'),
            'accept_header' => $request->header('Accept'),
        ]);

        try {
            // Log individual field values
            \Log::info('Cart add field analysis', [
                'product_id_raw' => $request->input('product_id'),
                'product_id_type' => gettype($request->input('product_id')),
                'quantity_raw' => $request->input('quantity'),
                'quantity_type' => gettype($request->input('quantity')),
                'size_raw' => $request->input('size'),
                'size_type' => gettype($request->input('size')),
                'color_raw' => $request->input('color'),
                'color_type' => gettype($request->input('color')),
                'all_input_keys' => array_keys($request->all()),
                'all_input_values' => $request->all(),
            ]);

            // Fallback: infer product_id from Referer slug when not provided
            if (empty($request->input('product_id'))) {
                \Log::warning('Product ID is empty, attempting to infer from referer');
                $referer = $request->header('Referer');
                if ($referer) {
                    $path = parse_url($referer, PHP_URL_PATH);
                    if (is_string($path)) {
                        $segments = array_values(array_filter(explode('/', $path)));
                        // Expect something like /product/{slug}
                        $slugIndex = array_search('product', $segments);
                        if ($slugIndex !== false && isset($segments[$slugIndex + 1])) {
                            $slug = $segments[$slugIndex + 1];
                            $inferredProduct = BarongProduct::where('slug', $slug)->first();
                            if ($inferredProduct) {
                                \Log::info('Successfully inferred product_id from referer slug', [
                                    'slug' => $slug,
                                    'inferred_product_id' => $inferredProduct->id,
                                    'product_name' => $inferredProduct->name,
                                ]);
                                $request->merge(['product_id' => $inferredProduct->id]);
                            } else {
                                \Log::warning('Failed to infer product from slug', [
                                    'slug' => $slug,
                                    'referer' => $referer,
                                    'path' => $path,
                                    'segments' => $segments,
                                ]);
                            }
                        } else {
                            \Log::warning('Could not parse product slug from referer', [
                                'referer' => $referer,
                                'path' => $path,
                                'segments' => $segments,
                            ]);
                        }
                    }
                } else {
                    \Log::warning('No referer header found');
                }
            }

            // Explicit validation to allow logging of failures
            $validatorInstance = \Validator::make($request->all(), [
                'product_id' => 'required|exists:barong_products,id',
                'quantity' => 'required|integer|min:1|max:10',
                // Allow flexible size labels; stock checks later ensure validity
                'size' => 'nullable|string|max:20',
                'color' => 'nullable|string|max:255',
            ]);

            // Log validation results
            if ($validatorInstance->fails()) {
                \Log::error('Cart add validation failed', [
                    'validation_errors' => $validatorInstance->errors()->toArray(),
                    'failed_rules' => $validatorInstance->failed(),
                    'input_data' => $request->all(),
                    'product_id_exists' => $request->input('product_id') ? BarongProduct::find($request->input('product_id')) !== null : false,
                    'available_products' => BarongProduct::pluck('id', 'slug')->toArray(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validatorInstance->errors(),
                    'debug_info' => [
                        'input_data' => $request->all(),
                        'available_products' => BarongProduct::pluck('id', 'slug')->toArray(),
                    ]
                ], 422);
            }

            $validator = $validatorInstance->validated();
            \Log::info('Validation passed', ['validated_data' => $validator]);

            $user = Auth::user();
            \Log::info('User authentication', [
                'user_id' => $user ? $user->id : 'guest',
                'user_email' => $user ? $user->email : 'guest',
                'is_authenticated' => Auth::check(),
            ]);

            $product = BarongProduct::findOrFail($validator['product_id']);
            \Log::info('Product found', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'is_available' => $product->is_available,
                'total_stock' => $product->getTotalStock(),
                'size_stocks' => $product->size_stocks,
            ]);

            if (!$product->is_available) {
                \Log::warning('Product not available', ['product_id' => $product->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Product is not available'
                ], 400);
            }

            // If product uses per-size stocks (check both size_stocks and color_stocks), enforce size selection and validate per-size availability
            $usesSizeStocks = (is_array($product->size_stocks) && count($product->size_stocks) > 0) || 
                             (is_array($product->color_stocks) && count($product->color_stocks) > 0);
            \Log::info('Stock validation debug', [
                'product_id' => $product->id,
                'uses_size_stocks' => $usesSizeStocks,
                'size_stocks' => $product->size_stocks,
                'color_stocks' => $product->color_stocks,
                'selected_size' => $validator['size'] ?? null,
                'requested_quantity' => $validator['quantity'],
            ]);
            
            if ($usesSizeStocks) {
                $selectedSize = $validator['size'] ?? null;
                if (!$selectedSize) {
                    // Auto-select when exactly one size is available
                    $availableSizes = [];
                    foreach (($product->size_stocks ?? []) as $sizeKey => $qty) {
                        if ((int) $qty > 0) { $availableSizes[] = (string) $sizeKey; }
                    }
                    if (count($availableSizes) === 1) {
                        $selectedSize = $availableSizes[0];
                    } else {
                        \Log::warning('No size selected for size-managed product', [
                            'product_id' => $product->id,
                            'size_stocks' => $product->size_stocks,
                            'available_sizes' => $availableSizes,
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Please select a size before adding to cart.'
                        ], 422);
                    }
                }

                $availableForSize = (int) ($product->getStockForSize($selectedSize) ?? 0);
                \Log::info('Size stock check', [
                    'product_id' => $product->id,
                    'size' => $selectedSize,
                    'available_stock' => $availableForSize,
                    'requested_quantity' => $validator['quantity'],
                ]);
                
                if ($validator['quantity'] > $availableForSize) {
                    \Log::warning('Insufficient stock for size', [
                        'product_id' => $product->id,
                        'size' => $selectedSize,
                        'requested_quantity' => $validator['quantity'],
                        'available_stock' => $availableForSize,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock for size ' . $selectedSize . '. Available: ' . $availableForSize
                    ], 400);
                }
            }

            if (!$usesSizeStocks && $validator['quantity'] > $product->getTotalStock()) {
                \Log::warning('Insufficient stock', [
                    'product_id' => $product->id,
                    'requested_quantity' => $validator['quantity'],
                    'available_stock' => $product->getTotalStock(),
                    'product_stock_column' => $product->stock,
                    'size_stocks_sum' => is_array($product->size_stocks) ? array_sum($product->size_stocks) : 'N/A',
                    'variations_sum' => $product->has_variations && is_array($product->variations) ? array_sum(array_column($product->variations, 'stock')) : 'N/A'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->getTotalStock()
                ], 400);
            }

            \Log::info('=== CART ADD REQUEST SUCCESS ===', [
                'product_id' => $product->id,
                'quantity' => $validator['quantity'],
                'size' => $validator['size'] ?? null,
                'color' => $validator['color'] ?? null,
                'user_id' => $user ? $user->id : 'guest',
            ]);

            // Continue with existing cart logic...
            if (!$user) {
                // Handle guest users with session cart
                $cart = session()->get('cart', []);

                if (isset($cart[$product->id])) {
                    // Prevent mixing different sizes for the same product in session cart
                    $existingSize = $cart[$product->id]['size'] ?? null;
                    $incomingSize = $validator['size'] ?? null;
                    if ($usesSizeStocks && $existingSize && $incomingSize && $existingSize !== $incomingSize) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This product is already in your cart with size ' . $existingSize . '. Remove it first to add a different size.'
                        ], 400);
                    }

                    $newQuantity = ($cart[$product->id]['quantity'] ?? 0) + $validator['quantity'];
                    if ($usesSizeStocks) {
                        $sizeToUse = $existingSize ?: $incomingSize;
                        $availableForSize = (int) ($product->getStockForSize($sizeToUse) ?? 0);
                        if ($newQuantity > $availableForSize) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Insufficient stock for size ' . $sizeToUse . '. Available: ' . $availableForSize . ', Already in cart: ' . ($cart[$product->id]['quantity'] ?? 0)
                            ], 400);
                        }
                    } else {
                        if ($newQuantity > $product->getTotalStock()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Insufficient stock. Available: ' . $product->getTotalStock() . ', Already in cart: ' . ($cart[$product->id]['quantity'] ?? 0)
                            ], 400);
                        }
                    }

                    $cart[$product->id]['quantity'] = $newQuantity;
                } else {
                    $cart[$product->id] = [
                        'quantity' => $validator['quantity'],
                        'size' => $validator['size'] ?? null,
                        'color' => $validator['color'] ?? null,
                        'added_at' => now()
                    ];
                }

                session()->put('cart', $cart);

                return response()->json([
                    'success' => true,
                    'message' => 'Product added to cart successfully',
                    'data' => [
                        'product' => $product,
                        'quantity' => $cart[$product->id]['quantity'],
                        'is_guest' => true
                    ]
                ]);
            }

            // Check if item already exists in cart
            $existingCartItem = Cart::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            if ($existingCartItem) {
                // Update quantity
                $newQuantity = $existingCartItem->quantity + $validator['quantity'];

                // Enforce size consistency and per-size stock constraint
                if ($usesSizeStocks) {
                    $existingSize = $existingCartItem->size;
                    $incomingSize = $validator['size'] ?? null;
                    if ($existingSize && $incomingSize && $existingSize !== $incomingSize) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This product is already in your cart with size ' . $existingSize . '. Remove it first to add a different size.'
                        ], 400);
                    }

                    $sizeToUse = $existingSize ?: $incomingSize;
                    if (!$sizeToUse) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please select a size before adding to cart.'
                        ], 422);
                    }

                    $availableForSize = (int) ($product->getStockForSize($sizeToUse) ?? 0);
                    if ($newQuantity > $availableForSize) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock for size ' . $sizeToUse . '. Available: ' . $availableForSize . ', Already in cart: ' . $existingCartItem->quantity
                        ], 400);
                    }
                } else {
                    if ($newQuantity > $product->getTotalStock()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock. Available: ' . $product->getTotalStock() . ', Already in cart: ' . $existingCartItem->quantity
                        ], 400);
                    }
                }

                // Calculate wholesale price based on total cart quantity
                $totalCartQuantity = $user->cart()->where('id', '!=', $existingCartItem->id)->sum('quantity') + $newQuantity;
                $finalPrice = $product->getPriceForQuantity($totalCartQuantity);
                
                $existingCartItem->update([
                    'quantity' => $newQuantity,
                    'price' => $finalPrice // Update price with wholesale pricing if applicable
                ]);

                $cartItem = $existingCartItem;
            } else {
                // Calculate wholesale price based on total cart quantity
                $totalCartQuantity = $user->cart()->sum('quantity') + $validator['quantity'];
                $finalPrice = $product->getPriceForQuantity($totalCartQuantity);
                
                // Create new cart item
                $cartItem = Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $validator['quantity'],
                    'price' => $finalPrice,
                    'size' => $validator['size'] ?? null,
                    'color' => $validator['color'] ?? null
                ]);
            }

            $cartItem->load('product');

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'data' => [
                    'cart_item' => $cartItem,
                    'product' => $cartItem->product,
                    'quantity' => $cartItem->quantity
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart add error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::user() ? Auth::user()->id : 'guest',
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
            $validator = $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string|in:S,M,L,XL,XXL',
            'color' => 'nullable|string'
        ]);

        try {
            $user = Auth::user();
            
            if (!$user) {
                // Handle guest users with session cart
                $cart = session()->get('cart', []);
                $productId = $validator['item_id'];
                
                if (!isset($cart[$productId])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cart item not found'
                    ], 404);
                }

                $product = BarongProduct::find($productId);
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found'
                    ], 404);
                }

                $usesSizeStocks = is_array($product->size_stocks) && count($product->size_stocks) > 0;
                if ($usesSizeStocks) {
                    $sizeInCart = $cart[$productId]['size'] ?? null;
                    if (!$sizeInCart) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please select a size for this cart item.'
                        ], 422);
                    }
                    $availableForSize = (int) ($product->getStockForSize($sizeInCart) ?? 0);
                    if ($validator['quantity'] > $availableForSize) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock for size ' . $sizeInCart . '. Available: ' . $availableForSize
                        ], 400);
                    }
                } else {
                    if ($validator['quantity'] > $product->getTotalStock()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock. Available: ' . $product->getTotalStock()
                        ], 400);
                    }
                }

                // Update quantity
                $cart[$productId]['quantity'] = $validator['quantity'];
                
                // Update size if provided
                if (isset($validator['size'])) {
                    $newSize = $validator['size'];
                    $usesSizeStocks = is_array($product->size_stocks) && count($product->size_stocks) > 0;
                    
                    if ($usesSizeStocks && $newSize) {
                        // Validate new size has stock
                        $availableForSize = (int) ($product->getStockForSize($newSize) ?? 0);
                        if ($validator['quantity'] > $availableForSize) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Insufficient stock for size ' . $newSize . '. Available: ' . $availableForSize
                            ], 400);
                        }
                    }
                    
                    $cart[$productId]['size'] = $newSize;
                }
                
                // Update color if provided
                if (isset($validator['color'])) {
                    $cart[$productId]['color'] = $validator['color'];
                }
                
                session()->put('cart', $cart);

                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated successfully',
                    'data' => [
                        'id' => $productId,
                        'product_id' => $productId,
                        'quantity' => $validator['quantity'],
                        'size' => $cart[$productId]['size'] ?? null,
                        'color' => $cart[$productId]['color'] ?? null,
                        'is_guest' => true
                    ]
                ]);
            }

            $cartItem = Cart::where('user_id', $user->id)
                ->where('id', $validator['item_id'])
                ->with('product')
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            $product = $cartItem->product;

            // Handle size update if provided
            $sizeToUse = isset($validator['size']) ? $validator['size'] : $cartItem->size;
            
            // If size is being changed, check if we can allow it
            if (isset($validator['size']) && $cartItem->size !== $validator['size']) {
                // Check if another cart item with this product and new size exists
                $existingWithNewSize = Cart::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->where('size', $validator['size'])
                    ->where('id', '!=', $cartItem->id)
                    ->first();
                
                if ($existingWithNewSize) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This product is already in your cart with size ' . $validator['size'] . '. Remove it first to change size.'
                    ], 400);
                }
                
                $sizeToUse = $validator['size'];
            }
            
            $usesSizeStocks = is_array($product->size_stocks) && count($product->size_stocks) > 0;
            if ($usesSizeStocks) {
                if (!$sizeToUse) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a size for this cart item.'
                    ], 422);
                }
                $availableForSize = (int) ($product->getStockForSize($sizeToUse) ?? 0);
                if ($validator['quantity'] > $availableForSize) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock for size ' . $sizeToUse . '. Available: ' . $availableForSize
                    ], 400);
                }
            } else {
                if ($validator['quantity'] > $product->getTotalStock()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock. Available: ' . $product->getTotalStock()
                    ], 400);
                }
            }

            // Calculate wholesale price based on total cart quantity
            $totalCartQuantity = $user->cart()->where('id', '!=', $cartItem->id)->sum('quantity') + $validator['quantity'];
            $finalPrice = $product->getPriceForQuantity($totalCartQuantity);
            
            // Prepare update data
            $updateData = [
                'quantity' => $validator['quantity'],
                'price' => $finalPrice // Update price with wholesale pricing if applicable
            ];
            
            // Update size if provided
            if (isset($validator['size'])) {
                $updateData['size'] = $validator['size'];
            }
            
            // Update color if provided
            if (isset($validator['color'])) {
                $updateData['color'] = $validator['color'];
            }
            
            $cartItem->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => [
                    'cart_item' => $cartItem,
                    'product' => $product,
                    'quantity' => $validator['quantity']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        \Log::info('Cart remove request received', [
            'method' => $request->method(),
            'path' => $request->path(),
            'payload' => $request->all(),
        ]);

        // Accept either item_id (cart item id) or product_id (for guests or fallback)
        $itemId = $request->input('item_id');
        $productId = $request->input('product_id') ?? $request->input('id');

        if (is_null($itemId) && is_null($productId)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing identifier: provide item_id or product_id',
            ], 422);
        }

        $user = Auth::user();

        try {
            if (!$user) {
                // Handle guest users with session cart
                $cart = session()->get('cart', []);
                $productId = $productId ?? $itemId; // for guests, id refers to product id
                
                if (!isset($cart[$productId])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cart item not found'
                    ], 404);
                }

                unset($cart[$productId]);
                session()->put('cart', $cart);

                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from cart successfully',
                    'data' => [
                        'id' => $productId,
                        'product_id' => $productId,
                        'is_guest' => true
                    ]
                ]);
            }

            // For authenticated users, prefer item_id; if only product_id supplied, resolve to cart item
            if ($itemId) {
                $cartItem = Cart::where('user_id', $user->id)
                    ->where('id', $itemId)
                    ->first();
            } else {
                $cartItem = Cart::where('user_id', $user->id)
                    ->where('product_id', $productId)
                    ->first();
            }

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to clear cart'
                ], 401);
            }

            Cart::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart summary
     */
    public function summary()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_quantity' => 0,
                        'total_amount' => 0,
                        'item_count' => 0
                    ]
                ]);
            }

            $cartItems = Cart::with('product')
                ->where('user_id', $user->id)
                ->active()
                ->get();

            $totalQuantity = $cartItems->sum('quantity');
            $totalAmount = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_quantity' => $totalQuantity,
                    'total_amount' => $totalAmount,
                    'item_count' => $cartItems->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart count for header display
     */
    public function count()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                // For guest users, count session cart items
                $sessionCart = session()->get('cart', []);
                $count = count($sessionCart);
                
                return response()->json([
                    'success' => true,
                    'data' => ['count' => $count]
                ]);
            }

            $count = Cart::where('user_id', $user->id)->active()->count();

            return response()->json([
                'success' => true,
                'data' => ['count' => $count]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync session cart with database cart (for when user logs in)
     */
    public function sync(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to sync cart'
                ], 401);
            }

            $sessionCart = session()->get('cart', []);
            
            if (empty($sessionCart)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No session cart to sync'
                ]);
            }

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
                                'quantity' => min($details['quantity'], $product->getTotalStock()),
                                'price' => $product->current_price
                            ]);
                        }
                    } else {
                        // Create new cart item
                        Cart::create([
                            'user_id' => $user->id,
                            'product_id' => $productId,
                            'quantity' => min($details['quantity'], $product->getTotalStock()),
                            'price' => $product->current_price
                        ]);
                    }
                }
            }

            // Clear session cart after sync
            session()->forget('cart');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart synced successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Apply coupon code
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to apply coupon'
                ], 401);
            }

            // Simple coupon validation (you can expand this)
            $validCoupons = [
                'WELCOME10' => 0.10, // 10% discount
                'SUMMER20' => 0.20,  // 20% discount
                'FREESHIP' => 0.00,  // Free shipping (already free)
            ];

            $couponCode = strtoupper($request->coupon_code);
            
            if (!isset($validCoupons[$couponCode])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coupon code'
                ]);
            }

            // Store coupon in session for now (you can create a coupons table later)
            session(['applied_coupon' => [
                'code' => $couponCode,
                'discount' => $validCoupons[$couponCode]
            ]]);

            $discountPercent = $validCoupons[$couponCode] * 100;
            return response()->json([
                'success' => true,
                'message' => "Coupon applied! You get {$discountPercent}% discount."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply coupon',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}