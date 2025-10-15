<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use App\Models\Brand;
use App\Models\BarongProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Requests\StoreBarongProductRequest;
use App\Services\CloudinaryService;

class AdminController extends Controller
{
    protected $cloudinaryService;
    
    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }
    
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_products' => BarongProduct::count(),
            'active_products' => BarongProduct::available()->count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_users' => User::count(),
            'total_reviews' => Review::count(),
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top selling barong products
        $topProducts = BarongProduct::with(['brand'])
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly sales data - PostgreSQL compatible
        $monthlySales = Order::select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'monthlySales'));
    }

    /**
     * Show barong products management (replacing old products system)
     */
    public function barongProducts(Request $request)
    {
        $query = BarongProduct::with(['brand', 'category']);

        // Filter by brand
        if ($request->has('brand') && $request->brand) {
            $query->where('brand_id', $request->brand);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'available') {
                $query->where('is_available', true);
            } elseif ($request->status === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $barongProducts = $query->orderBy('created_at', 'desc')->paginate(20);
        $brands = Brand::active()->get();
        $categories = Category::active()->get();

        return view('admin.products', compact('barongProducts', 'brands', 'categories'));
    }

    /**
     * Show create barong product form
     */
    public function createBarongProduct()
    {
        $brands = Brand::active()->get();
        $categories = Category::active()->get();
        
        return view('admin.barong-product-form', compact('brands', 'categories'));
    }

    /**
     * Store new barong product
     */
    
    public function storeBarongProduct(StoreBarongProductRequest $request)
    {
        // Log that the request reached the controller
        Log::info('=== PRODUCT CREATION REQUEST RECEIVED ===', [
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
        ]);

        // Log all request data (excluding files for readability)
        Log::info('Request Data Received:', [
            'form_data' => $request->except(['images', 'new_images']),
            'has_images' => $request->hasFile('images'),
            'has_new_images' => $request->hasFile('new_images'),
            'image_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'new_image_count' => $request->hasFile('new_images') ? count($request->file('new_images')) : 0,
        ]);

        // Log file details if present
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                Log::info("Image {$index} details:", [
                    'filename' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_valid' => $file->isValid(),
                ]);
            }
        }

        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $index => $file) {
                Log::info("New Image {$index} details:", [
                    'filename' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_valid' => $file->isValid(),
                ]);
            }
        }

        try {
            Log::info('Starting database transaction...');
            DB::beginTransaction();

            // Use mass assignment with only fillable fields
            $productData = $request->only([
                'name', 'description', 'brand_id', 'category_id',
                'sleeve_type', 'base_price', 'special_price', 'stock',
                'size_stocks', 'fabric', 'embroidery_style', 'colors', 'collar_type',
                'design_details', 'video_url', 'variations'
            ]);
            
            // Set default type since it's required by database
            $productData['type'] = 'Traditional Barong';

            Log::info('Product data prepared:', [
                'product_data' => $productData,
                'data_keys' => array_keys($productData),
            ]);

            // Process images using helper methods
            Log::info('Processing images...');
            $productData['images'] = $this->processImages($request);
            $productData['cover_image'] = $this->setCoverImage($productData['images'], $request->cover_image_index);

            Log::info('Images processed:', [
                'image_paths' => $productData['images'],
                'cover_image' => $productData['cover_image'],
                'cover_image_index' => $request->cover_image_index,
            ]);

            // Variations disabled: ensure cleared and rely on size_stocks
            $productData['variations'] = [];
            $productData['has_variations'] = false;

            // Set defaults using Eloquent
            $productData['is_available'] = true;
            $productData['is_featured'] = $request->boolean('is_featured', true);
            
            // Calculate total stock from size stocks
            if (isset($productData['size_stocks']) && is_array($productData['size_stocks'])) {
                $productData['stock'] = array_sum($productData['size_stocks']);
                Log::info('Total stock calculated from size stocks:', [
                    'size_stocks' => $productData['size_stocks'],
                    'total_stock' => $productData['stock']
                ]);
            } else {
                $productData['stock'] = $productData['has_variations'] ? 0 : ($productData['stock'] ?? 0);
            }

            Log::info('Final product data before creation:', [
                'final_data' => $productData,
                'is_available' => $productData['is_available'],
                'is_featured' => $productData['is_featured'],
                'stock' => $productData['stock'],
            ]);

            // Create product with relationships
            Log::info('Creating BarongProduct...');
            $barongProduct = BarongProduct::create($productData);

            Log::info('Product created successfully:', [
                'product_id' => $barongProduct->id,
                'product_name' => $barongProduct->name,
                'product_sku' => $barongProduct->sku,
                'product_slug' => $barongProduct->slug,
            ]);

            DB::commit();
            Log::info('Database transaction committed successfully');

            Log::info('Product creation completed successfully', [
                'product_id' => $barongProduct->id,
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Barong product created successfully',
                'product' => $barongProduct->load(['brand', 'category'])
            ]);

        } catch (\Exception $e) {
            Log::error('Product creation failed - rolling back transaction', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);

            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show edit barong product form
     */
    public function editBarongProduct($id)
    {
        $barongProduct = BarongProduct::with(['brand', 'category'])->findOrFail($id);
        $brands = Brand::active()->get();
        $categories = Category::active()->get();
        
        return view('admin.barong-product-form', compact('barongProduct', 'brands', 'categories'));
    }

    /**
     * Update barong product
     */
    public function updateBarongProduct(Request $request, $id)
    {
        $barongProduct = BarongProduct::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:barong_products,name,' . $id,
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'size_stocks' => 'nullable|array',
            'size_stocks.S' => 'nullable|integer|min:0',
            'size_stocks.M' => 'nullable|integer|min:0',
            'size_stocks.L' => 'nullable|integer|min:0',
            'size_stocks.XL' => 'nullable|integer|min:0',
            'size_stocks.XXL' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'has_variations' => 'boolean',
            'fabric' => 'nullable|array',
            'embroidery_style' => 'nullable|array',
            'colors' => 'nullable|array',
            'sleeve_type' => 'nullable|string',
            'video_url' => 'nullable|url',
            'variations' => 'nullable|array',
            'variations.*.sku' => 'nullable|string|max:255|unique:barong_products,sku,' . $id,
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image_index' => 'nullable|integer|min:0',
        ], [
            'name.unique' => 'A product with this name already exists. Please choose a different name.',
            'variations.*.sku.unique' => 'A product with this SKU already exists. Please choose a different SKU.',
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'brand_id.required' => 'Brand selection is required.',
            'brand_id.exists' => 'Selected brand does not exist.',
            'category_id.required' => 'Category selection is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'base_price.required' => 'Base price is required.',
            'base_price.numeric' => 'Base price must be a valid number.',
            'base_price.min' => 'Base price cannot be negative.',
            'special_price.numeric' => 'Special price must be a valid number.',
            'special_price.min' => 'Special price cannot be negative.',
            'stock.integer' => 'Stock must be a whole number.',
            'stock.min' => 'Stock cannot be negative.',
            'size_stocks.S.integer' => 'Size S stock must be a whole number.',
            'size_stocks.S.min' => 'Size S stock cannot be negative.',
            'size_stocks.M.integer' => 'Size M stock must be a whole number.',
            'size_stocks.M.min' => 'Size M stock cannot be negative.',
            'size_stocks.L.integer' => 'Size L stock must be a whole number.',
            'size_stocks.L.min' => 'Size L stock cannot be negative.',
            'size_stocks.XL.integer' => 'Size XL stock must be a whole number.',
            'size_stocks.XL.min' => 'Size XL stock cannot be negative.',
            'size_stocks.XXL.integer' => 'Size XXL stock must be a whole number.',
            'size_stocks.XXL.min' => 'Size XXL stock cannot be negative.',
            'images.max' => 'You can upload a maximum of 8 images.',
            'images.*.image' => 'Uploaded files must be valid images.',
            'images.*.mimes' => 'Images must be in JPEG, PNG, JPG, or GIF format.',
            'images.*.max' => 'Each image must be smaller than 2MB.',
            'video_url.url' => 'Please enter a valid video URL.',
        ]);

        try {
            DB::beginTransaction();

            // Use mass assignment with only fillable fields
            $productData = $request->only([
                'name', 'description', 'brand_id', 'category_id',
                'sleeve_type', 'base_price', 'special_price', 'stock',
                'size_stocks', 'fabric', 'embroidery_style', 'colors', 'collar_type',
                'design_details', 'video_url', 'variations'
            ]);
            
            // Set default type since it's required by database
            $productData['type'] = 'Traditional Barong';

            // Process images (existing + new)
            $allImages = $barongProduct->images ?? [];
            
            // Add new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $result = $this->cloudinaryService->uploadImage($image, '3migs-products');
                        if ($result['success']) {
                            $allImages[] = $result['url'];
                            Log::info("New image uploaded to Cloudinary:", [
                                'original_name' => $image->getClientOriginalName(),
                                'cloudinary_url' => $result['url'],
                                'public_id' => $result['public_id'],
                            ]);
                        } else {
                            Log::error("New image upload to Cloudinary failed:", [
                                'original_name' => $image->getClientOriginalName(),
                                'error' => $result['error'],
                            ]);
                        }
                    }
                }
            }
            
            $productData['images'] = $allImages;
            
            // Set cover image based on index
            if ($request->has('cover_image_index') && !empty($allImages)) {
                $coverIndex = (int) $request->cover_image_index;
                Log::info('Cover image update:', [
                    'cover_index' => $coverIndex,
                    'total_images' => count($allImages),
                    'all_images' => $allImages,
                ]);
                
                if (isset($allImages[$coverIndex])) {
                    $productData['cover_image'] = $allImages[$coverIndex];
                    Log::info('Cover image set:', [
                        'cover_image' => $productData['cover_image'],
                        'cover_index' => $coverIndex,
                    ]);
                } else {
                    Log::warning('Cover image index out of range:', [
                        'cover_index' => $coverIndex,
                        'max_index' => count($allImages) - 1,
                    ]);
                }
            } else {
                Log::info('No cover image index provided or no images available');
            }

            // Variations disabled in UI: always clear and rely on size_stocks
            $productData['variations'] = [];
            $productData['has_variations'] = false;

            Log::info('Variations processing completed:', [
                'has_variations' => $productData['has_variations'],
                'variations_count' => isset($productData['variations']) ? count($productData['variations']) : 0,
                'request_has_variations' => $request->has('has_variations'),
                'request_has_variations_value' => $request->input('has_variations'),
            ]);

            // Set defaults
            $productData['is_available'] = $request->boolean('is_available', true);
            $productData['is_featured'] = $request->boolean('is_featured', false);
            
            // Calculate total stock from size stocks
            if (isset($productData['size_stocks']) && is_array($productData['size_stocks'])) {
                // Normalize to integers before summing
                $normalized = array_map('intval', $productData['size_stocks']);
                $productData['size_stocks'] = $normalized;
                $productData['stock'] = array_sum($normalized);
                Log::info('Total stock calculated from size stocks (update):', [
                    'size_stocks' => $normalized,
                    'total_stock' => $productData['stock']
                ]);
            } else {
                $productData['stock'] = $productData['has_variations'] ? 0 : ($productData['stock'] ?? 0);
            }

            $barongProduct->update($productData);

            DB::commit();

            // Clear any caches that might affect product display
            \Illuminate\Support\Facades\Cache::forget("product_{$barongProduct->id}");
            \Illuminate\Support\Facades\Cache::forget("product_slug_{$barongProduct->slug}");
            
            // Clear view cache
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            
            Log::info('Product updated successfully with cache clearing:', [
                'product_id' => $barongProduct->id,
                'product_slug' => $barongProduct->slug,
                'size_stocks' => $barongProduct->size_stocks,
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Barong product updated successfully',
                'product' => $barongProduct->load(['brand', 'category']),
                'size_stocks' => $barongProduct->size_stocks,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete barong product
     */
    public function deleteBarongProduct($id)
    {
        $barongProduct = BarongProduct::findOrFail($id);
        $productName = $barongProduct->name; // Store name before deletion
        $barongProduct->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barong product deleted successfully',
            'product_name' => $productName
        ]);
    }

    /**
     * Show orders management
     */
    public function orders(Request $request)
    {
        $query = Order::with(['user', 'orderItems.product']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    /**
     * Update an order's status (admin only)
     */
    public function updateOrderStatus(Request $request, int $id)
    {
        // Align allowed status values with DB enum (no 'completed' if DB doesn't allow it)
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded,expired',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $status = (string) $request->string('status');
        // Map UI 'completed' to 'delivered' to satisfy DB constraint
        if ($status === 'completed') { $status = 'delivered'; }
        $order->status = $status;
        if ($request->filled('payment_status')) {
            $order->payment_status = $request->string('payment_status');
        }

        // Timestamps for lifecycle events
        if ($order->status === 'shipped' && empty($order->shipped_at)) {
            $order->shipped_at = now();
        }
        if (in_array($order->status, ['delivered', 'completed'], true) && empty($order->delivered_at)) {
            $order->delivered_at = now();
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order,
        ]);
    }

    /**
     * Show users management
     */
    public function users(Request $request)
    {
        $query = User::with('roles');

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Show reviews management
     */
    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'product']);

        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        // Filter by verified purchases
        if ($request->has('verified') && $request->verified) {
            $query->where('is_verified_purchase', $request->verified);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reviews', compact('reviews'));
    }

    /**
     * Process uploaded images using Cloudinary
     */
    private function processImages(StoreBarongProductRequest $request): array
    {
        Log::info('processImages method called', [
            'has_images' => $request->hasFile('images'),
            'has_new_images' => $request->hasFile('new_images'),
        ]);

        $imageUrls = [];
        
        // Process existing images
        if ($request->hasFile('images')) {
            Log::info('Processing existing images with Cloudinary...');
            foreach ($request->file('images') as $index => $image) {
                if ($image->isValid()) {
                    $result = $this->cloudinaryService->uploadImage($image, '3migs-products');
                    if ($result['success']) {
                        $imageUrls[] = $result['url'];
                        Log::info("Image {$index} uploaded to Cloudinary successfully:", [
                            'original_name' => $image->getClientOriginalName(),
                            'cloudinary_url' => $result['url'],
                            'public_id' => $result['public_id'],
                            'size' => $image->getSize(),
                        ]);
                    } else {
                        Log::error("Image {$index} upload to Cloudinary failed:", [
                            'original_name' => $image->getClientOriginalName(),
                            'error' => $result['error'],
                        ]);
                    }
                } else {
                    Log::warning("Image {$index} is invalid:", [
                        'original_name' => $image->getClientOriginalName(),
                        'error' => $image->getError(),
                    ]);
                }
            }
        }
        
        // Process new images
        if ($request->hasFile('new_images')) {
            Log::info('Processing new images with Cloudinary...');
            foreach ($request->file('new_images') as $index => $image) {
                if ($image->isValid()) {
                    $result = $this->cloudinaryService->uploadImage($image, '3migs-products');
                    if ($result['success']) {
                        $imageUrls[] = $result['url'];
                        Log::info("New Image {$index} uploaded to Cloudinary successfully:", [
                            'original_name' => $image->getClientOriginalName(),
                            'cloudinary_url' => $result['url'],
                            'public_id' => $result['public_id'],
                            'size' => $image->getSize(),
                        ]);
                    } else {
                        Log::error("New Image {$index} upload to Cloudinary failed:", [
                            'original_name' => $image->getClientOriginalName(),
                            'error' => $result['error'],
                        ]);
                    }
                } else {
                    Log::warning("New Image {$index} is invalid:", [
                        'original_name' => $image->getClientOriginalName(),
                        'error' => $image->getError(),
                    ]);
                }
            }
        }
        
        Log::info('Image processing completed:', [
            'total_images' => count($imageUrls),
            'image_urls' => $imageUrls,
        ]);
        
        return $imageUrls;
    }

    /**
     * Set cover image from image array
     */
    private function setCoverImage(array $images, ?int $coverIndex = 0): ?string
    {
        if (empty($images)) {
            return null;
        }
        
        $index = $coverIndex ?? 0;
        return $images[$index] ?? $images[0];
    }

    /**
     * Process variations using Eloquent collections
     */
    private function processVariations(array $variations): array
    {
        Log::info('processVariations method called', [
            'input_variations' => $variations,
            'variation_count' => count($variations),
        ]);

        $processedVariations = collect($variations)
            ->filter(fn($variation) => !empty($variation['size']) && !empty($variation['color']))
            ->map(function ($variation, $index) {
                Log::info("Processing variation {$index}:", [
                    'size' => $variation['size'] ?? 'empty',
                    'color' => $variation['color'] ?? 'empty',
                    'price' => $variation['price'] ?? 'empty',
                    'stock' => $variation['stock'] ?? 'empty',
                    'sku' => $variation['sku'] ?? 'empty',
                ]);

                if (empty($variation['sku'])) {
                    $variation['sku'] = 'BRG-' . strtoupper(Str::random(8)) . '-' . 
                        strtoupper(substr($variation['size'], 0, 2)) . '-' . 
                        strtoupper(substr($variation['color'], 0, 2));
                    
                    Log::info("Generated SKU for variation {$index}:", [
                        'generated_sku' => $variation['sku'],
                    ]);
                }
                return $variation;
            })
            ->values()
            ->toArray();

        Log::info('Variations processing completed:', [
            'processed_variations' => $processedVariations,
            'final_count' => count($processedVariations),
        ]);

        return $processedVariations;
    }

    /**
     * Display the sales page
     */
    public function sales(Request $request)
    {
        // Get sales data
        $totalSales = 0; // TODO: Calculate from orders
        $monthlySales = 0; // TODO: Calculate monthly sales
        $topProducts = collect(); // TODO: Get top selling products
        
        return view('admin.sales', compact('totalSales', 'monthlySales', 'topProducts'));
    }

    /**
     * Display the inventory page
     */
    public function inventory(Request $request)
    {
        // To reduce redundancy, use the unified products panel
        return redirect()->route('admin.products');
    }

    /**
     * Display the coupons page
     */
    public function coupons(Request $request)
    {
        // TODO: Implement coupon system
        $coupons = collect(); // Placeholder for coupons
        
        return view('admin.coupons', compact('coupons'));
    }

    /**
     * Display the reports page
     */
    public function reports(Request $request)
    {
        // Sales by month (last 12 months) - PostgreSQL compatible
        $driver = DB::getDriverName();
        $ymExpr = $driver === 'pgsql' ? 'TO_CHAR(created_at, \'YYYY-MM\')' : 'DATE_FORMAT(created_at, "%Y-%m")';

        $sales_by_month = Order::select(
                DB::raw($ymExpr . ' as ym'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereIn('status', ['completed', 'delivered', 'shipped'])
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->map(function ($row) {
                return (object) [
                    'month' => $row->ym,
                    'orders_count' => (int) $row->orders_count,
                    'revenue' => (float) $row->revenue,
                ];
            });

        $total_revenue = (float) Order::whereIn('status', ['completed', 'delivered'])->sum('total_amount');
        $total_orders = (int) Order::whereIn('status', ['completed', 'delivered'])->count();
        $average_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;

        // Best selling products (by quantity)
        $best_sellers = DB::table('order_items')
            ->join('barong_products', 'order_items.product_id', '=', 'barong_products.id')
            ->select('product_id', 'barong_products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.total_price) as total_sales'))
            ->groupBy('product_id', 'barong_products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $salesReport = [
            'total_revenue' => $total_revenue,
            'total_orders' => $total_orders,
            'average_order_value' => $average_order_value,
            'top_customers' => collect(), // can be filled later
            'sales_by_month' => $sales_by_month,
            'best_sellers' => $best_sellers,
        ];

        return view('admin.reports', compact('salesReport'));
    }

    /**
     * Print-friendly reports view
     */
    public function reportsPrint(Request $request)
    {
        // Sales by month (last 12 months) - PostgreSQL compatible
        $driver = DB::getDriverName();
        $ymExpr = $driver === 'pgsql' ? 'TO_CHAR(created_at, \'YYYY-MM\')' : 'DATE_FORMAT(created_at, "%Y-%m")';

        $sales_by_month = Order::select(
                DB::raw($ymExpr . ' as ym'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereIn('status', ['completed', 'delivered', 'shipped'])
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->map(function ($row) {
                return (object) [
                    'month' => $row->ym,
                    'orders_count' => (int) $row->orders_count,
                    'revenue' => (float) $row->revenue,
                ];
            });

        $total_revenue = (float) Order::whereIn('status', ['completed', 'delivered'])->sum('total_amount');
        $total_orders = (int) Order::whereIn('status', ['completed', 'delivered'])->count();
        $average_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;

        // Top customers by total spent
        $top_customers = User::select('users.*')
            ->selectRaw('COUNT(orders.id) as orders_count')
            ->selectRaw('SUM(orders.total_amount) as total_spent')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.updated_at')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $salesReport = [
            'total_revenue' => $total_revenue,
            'total_orders' => $total_orders,
            'average_order_value' => $average_order_value,
            'top_customers' => $top_customers,
            'sales_by_month' => $sales_by_month,
        ];

        return view('admin.reports-print', compact('salesReport'));
    }

    /**
     * Export reports as CSV (download)
     */
    public function reportsExportCsv(Request $request)
    {
        // Example dataset – replace with real queries (orders, totals, etc.)
        $rows = [
            ['Report', 'Value'],
            ['Total Revenue', 0],
            ['Total Orders', 0],
            ['Average Order Value', 0],
        ];

        $callback = function () use ($rows) {
            $FH = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        $filename = 'reports_' . now()->format('Ymd_His') . '.csv';
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Seed a test delivered order (for quickly validating charts/best sellers)
     */
    public function seedTestDelivery()
    {
        // Create a small delivered order with two items using existing products
        $product = BarongProduct::first();
        if (!$product) {
            return back()->with('error', 'No products available to create a test order.');
        }

        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $order = new Order();
        $order->order_number = Order::generateOrderNumber();
        $order->user_id = $user->id;
        $order->status = 'delivered';
        $order->payment_status = 'paid';
        $order->payment_method = 'test';
        $order->subtotal = $product->current_price * 2;
        $order->discount = 0;
        $order->shipping_fee = 0;
        $order->total_amount = $order->subtotal;
        $order->currency = 'PHP';
        $order->billing_address = [];
        $order->shipping_address = [];
        $order->delivered_at = now();
        $order->save();

        // Attach order item
        \DB::table('order_items')->insert([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->current_price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.reports')->with('success', 'Test delivered order created.');
    }

    /**
     * Delete all products and seed random test products (with and without stock)
     */
    public function seedTestProducts()
    {
        // Danger: wipe existing barong_products
        BarongProduct::query()->delete();

        $brands = Brand::all();
        $categories = Category::all();
        if ($brands->isEmpty() || $categories->isEmpty()) {
            return back()->with('error', 'Please ensure at least one brand and one category exist.');
        }

        $names = [
            'Classic Barong', 'Modern Barong', 'Heritage Barong', 'Wedding Barong', 'Casual Barong',
            'Handwoven Barong', 'Slim Fit Barong', 'Piña Barong', 'Cocoon Barong', 'Embroidered Barong'
        ];

        $faker = \Faker\Factory::create();
        $count = 12;
        for ($i = 0; $i < $count; $i++) {
            $sizeStocks = [];
            foreach (['S','M','L','XL','XXL'] as $sz) {
                // Half of the products get zero stock randomly
                $sizeStocks[$sz] = $faker->boolean(60) ? $faker->numberBetween(0, 8) : 0;
            }
            $stock = array_sum($sizeStocks);

            BarongProduct::create([
                'name' => $faker->randomElement($names) . ' ' . strtoupper($faker->bothify('##?')),
                'description' => $faker->sentence(8),
                'type' => 'Barong',
                'brand_id' => $brands->random()->id,
                'category_id' => $categories->random()->id,
                'images' => [],
                'cover_image' => null,
                'video_url' => null,
                'fabric' => [],
                'embroidery_style' => [],
                'colors' => [],
                'sleeve_type' => $faker->randomElement(['Short Sleeve','Long Sleeve']),
                'collar_type' => $faker->randomElement(['Chinese','Classic','Wing']),
                'design_details' => [],
                'base_price' => $faker->randomFloat(2, 1500, 4500),
                'special_price' => null,
                'stock' => $stock,
                'size_stocks' => $sizeStocks,
                'variations' => [],
                'is_available' => $stock > 0,
                'is_featured' => $faker->boolean(30),
                'has_variations' => false,
                'sku' => 'BRG-' . strtoupper(Str::random(8)),
                'sort_order' => $i + 1,
            ]);
        }

        return redirect()->route('admin.products')->with('success', 'Seeded random test products.');
    }

    /**
     * Seed multiple random delivered orders spread across the last months
     */
    public function seedRandomOrders()
    {
        $products = BarongProduct::take(10)->get();
        if ($products->isEmpty()) {
            return back()->with('error', 'No products available. Seed products first.');
        }
        $user = User::first() ?: User::factory()->create();
        $faker = \Faker\Factory::create();

        // Create 18 orders spread across the last 9 months
        for ($i = 0; $i < 18; $i++) {
            $when = now()->subMonths($faker->numberBetween(0, 8))->subDays($faker->numberBetween(0, 28));
            $items = $products->random($faker->numberBetween(1, min(3, $products->count())));

            $subtotal = 0;
            $order = new Order();
            $order->order_number = Order::generateOrderNumber();
            $order->user_id = $user->id;
            $order->status = 'delivered';
            $order->payment_status = 'paid';
            $order->payment_method = 'test';
            $order->discount = 0;
            $order->shipping_fee = 0;
            $order->currency = 'PHP';
            $order->billing_address = [];
            $order->shipping_address = [];
            $order->created_at = $when;
            $order->updated_at = $when;
            $order->delivered_at = $when;
            $order->save();

            foreach ($items as $product) {
                $qty = $faker->numberBetween(1, 3);
                $line = $product->current_price * $qty;
                $subtotal += $line;
                \DB::table('order_items')->insert([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $qty,
                    'unit_price' => $product->current_price,
                    'total_price' => $line,
                    'created_at' => $when,
                    'updated_at' => $when,
                ]);
            }

            $order->subtotal = $subtotal;
            $order->total_amount = $subtotal;
            $order->save();
        }

        return redirect()->route('admin.reports')->with('success', 'Random delivered orders seeded.');
    }

    /**
     * Display the admin settings page
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Handle password change request
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }
}