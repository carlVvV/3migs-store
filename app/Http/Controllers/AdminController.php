<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use App\Models\BarongProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use App\Http\Requests\StoreBarongProductRequest;
use App\Services\CloudinaryService;
use Faker\Factory as FakerFactory;
use Spatie\Permission\Models\Role;

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
        // Calculate total revenue from completed/delivered/shipped orders
        $total_revenue = Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->sum('total_amount');
        
        $stats = [
            'total_products' => BarongProduct::count(),
            'active_products' => BarongProduct::where('is_available', true)->count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_users' => User::count(),
            'total_reviews' => Review::count(),
            'total_revenue' => $total_revenue,
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top selling barong products
        $topProducts = BarongProduct::select('id', 'name', 'slug', 'is_featured', 'created_at')
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
        $query = BarongProduct::with(['category']);

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
        $categories = Category::active()->get();

        return view('admin.products', compact('barongProducts', 'categories'));
    }

    /**
     * Show create barong product form
     */
    public function createBarongProduct()
    {
        $categories = Category::active()->get();

        return view('admin.barong-product-form', compact('categories'));
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
                'name', 'description', 'category_id',
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

            // Check for low stock alert after creation
            $barongProduct->checkLowStockAlert();

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
                'product' => $barongProduct->load(['category'])
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
        $barongProduct = BarongProduct::with(['category'])->findOrFail($id);
        $categories = Category::active()->get();

        return view('admin.barong-product-form', compact('barongProduct', 'categories'));
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
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
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
            'category_id.required' => 'Category selection is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'base_price.required' => 'Base price is required.',
            'base_price.numeric' => 'Base price must be a valid number.',
            'base_price.min' => 'Base price cannot be negative.',
            'special_price.numeric' => 'Special price must be a valid number.',
            'special_price.min' => 'Special price cannot be negative.',
            'wholesale_price.numeric' => 'Wholesale price must be a valid number.',
            'wholesale_price.min' => 'Wholesale price cannot be negative.',
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
                'name', 'description', 'category_id',
                'sleeve_type', 'base_price', 'special_price', 'wholesale_price', 'stock',
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
            $productData['is_new_arrival'] = $request->boolean('is_new_arrival', false);
            
            // Handle color_stocks if provided
            if ($request->has('color_stocks') && is_array($request->input('color_stocks'))) {
                $colorStocks = [];
                foreach ($request->input('color_stocks') as $size => $colors) {
                    if (is_array($colors)) {
                        foreach ($colors as $color => $qty) {
                            if ($qty > 0) {
                                $colorStocks[$size][$color] = intval($qty);
                            }
                        }
                    }
                }
                $productData['color_stocks'] = $colorStocks;
                
                // Calculate total stock from color_stocks
                $totalFromColors = 0;
                foreach ($colorStocks as $size => $colors) {
                    if (is_array($colors)) {
                        foreach ($colors as $color => $qty) {
                            $totalFromColors += intval($qty);
                        }
                    }
                }
                $productData['stock'] = $totalFromColors;
                Log::info('Total stock calculated from color stocks (update):', [
                    'color_stocks' => $colorStocks,
                    'total_stock' => $productData['stock']
                ]);
            } elseif (isset($productData['size_stocks']) && is_array($productData['size_stocks'])) {
                // Calculate total stock from size stocks
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

            // Store old stock for low stock monitoring
            $oldStock = $barongProduct->getTotalStock();
            
            $barongProduct->update($productData);

            // Check for low stock alert after update
            $barongProduct->refresh();
            $barongProduct->checkLowStockAlert($oldStock);

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
                'product' => $barongProduct->load(['category']),
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
     * Delete barong product (soft delete)
     */
    public function deleteBarongProduct($id)
    {
        try {
            $barongProduct = BarongProduct::findOrFail($id);
            $productName = $barongProduct->name;
            $productSku = $barongProduct->sku;
            
            // Soft delete the product
            $barongProduct->delete();
            
            // Log the deletion
            \Log::info('Product soft deleted', [
                'product_id' => $id,
                'product_name' => $productName,
                'product_sku' => $productSku,
                'deleted_at' => now(),
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product moved to trash successfully. You can restore it from the deleted items section.',
                'product_name' => $productName
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to delete product', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'deleted_by' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show deleted items management
     */
    public function deletedItems(Request $request)
    {
        $query = BarongProduct::onlyTrashed()->with(['category']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $deletedProducts = $query->orderBy('deleted_at', 'desc')->paginate(20);

        return view('admin.deleted-items', compact('deletedProducts'));
    }

    /**
     * Restore a deleted product
     */
    public function restoreProduct($id)
    {
        try {
            $product = BarongProduct::onlyTrashed()->findOrFail($id);
            $product->restoreProduct();

            return response()->json([
                'success' => true,
                'message' => 'Product restored successfully',
                'product_name' => $product->name
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to restore product', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'restored_by' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete a product
     */
    public function forceDeleteProduct($id)
    {
        try {
            $product = BarongProduct::onlyTrashed()->findOrFail($id);
            $productName = $product->name;
            $product->forceDeleteProduct();

            return response()->json([
                'success' => true,
                'message' => 'Product permanently deleted',
                'product_name' => $productName
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to permanently delete product', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'deleted_by' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show orders management
     */
    public function orders(Request $request)
    {
        // Get regular orders
        $regularQuery = Order::with(['user', 'orderItems.product']);
        
        // Get custom design orders
        $customQuery = \App\Models\CustomDesignOrder::with('user');
        
        // Apply filters to both queries
        if ($request->has('status') && $request->status) {
            $regularQuery->where('status', $request->status);
            $customQuery->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $regularQuery->whereDate('created_at', '>=', $request->date_from);
            $customQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $regularQuery->whereDate('created_at', '<=', $request->date_to);
            $customQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get orders and combine them
        $regularOrders = $regularQuery->orderBy('created_at', 'desc')->get();
        $customOrders = $customQuery->orderBy('created_at', 'desc')->get();
        
        // Combine and sort by creation date
        $allOrders = $regularOrders->concat($customOrders)->sortByDesc('created_at');
        
        // Create a paginated collection
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $allOrders->slice($offset, $perPage)->values();
        
        $orders = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allOrders->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );
        
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
        $query = User::with(['roles', 'latestIdDocument', 'approvedIdDocument']);

        // Filter by role with graceful fallback when role does not exist in Spatie tables
        if ($request->filled('role')) {
            $roleFilter = $request->input('role');

            if ($roleFilter === 'admin') {
                $query->where('role', 'admin');
            } elseif ($roleFilter === 'user') {
                $query->where(function ($inner) {
                    $inner->whereNull('role')
                        ->orWhere('role', 'user')
                        ->orWhere('role', 'customer');
                });
            } elseif (Role::where('name', $roleFilter)->exists()) {
                $query->role($roleFilter);
            }
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
     * Fetch detailed information for a specific user (AJAX endpoint).
     */
    public function userDetails(User $user)
    {
        $user->load([
            'addresses' => fn ($query) => $query->orderByDesc('is_default')->orderByDesc('created_at'),
            'idDocuments' => fn ($query) => $query->latest(),
            'orders' => fn ($query) => $query->with(['orderItems.product'])->latest()->take(10),
        ]);

        $orderSummary = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_spent' => (float) Order::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'delivered'])
                ->sum('total_amount'),
        ];

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'is_admin' => $user->isAdmin(),
            'created_at' => optional($user->created_at)->toDateTimeString(),
            'email_verified_at' => optional($user->email_verified_at)->toDateTimeString(),
        ];

        $addresses = $user->addresses->map(function ($address) {
            return [
                'id' => $address->id,
                'label' => $address->label,
                'full_name' => $address->full_name,
                'street_address' => $address->street_address,
                'apartment' => $address->apartment,
                'city' => $address->city,
                'province' => $address->province,
                'region' => $address->region,
                'barangay' => $address->barangay,
                'postal_code' => $address->postal_code,
                'phone' => $address->phone,
                'email' => $address->email,
                'is_default' => (bool) $address->is_default,
                'created_at' => optional($address->created_at)->toDateTimeString(),
            ];
        });

        $idDocuments = $user->idDocuments->map(function ($document) {
            return [
                'id' => $document->id,
                'type' => $document->type,
                'status' => $document->status,
                'veriff_session_id' => $document->veriff_session_id,
                'uploaded_at' => optional($document->created_at)->toDateTimeString(),
            ];
        });

        $orders = $user->orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'total_amount' => (float) $order->total_amount,
                'created_at' => optional($order->created_at)->toDateTimeString(),
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name ?? optional($item->product)->name,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'total_price' => (float) $item->total_price,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $userData,
                'addresses' => $addresses,
                'id_documents' => $idDocuments,
                'orders' => $orders,
                'order_summary' => $orderSummary,
            ],
        ]);
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
        // Get low stock products (5 or less)
        $lowStockProducts = BarongProduct::getLowStockProducts(5);
        
        // Get out of stock products
        $outOfStockProducts = BarongProduct::getOutOfStockProducts();
        
        // Get all products for the table
        $query = BarongProduct::with(['category']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'available') {
                $query->where('is_available', true);
            } elseif ($request->status === 'unavailable') {
                $query->where('is_available', false);
            } elseif ($request->status === 'low_stock') {
                $query->where('is_available', true);
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

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get recent low stock notifications and update with current stock
        $recentNotifications = \App\Models\LowStockNotification::unresolved()
            ->recent(7)
            ->with('product')
            ->orderBy('notified_at', 'desc')
            ->get()
            ->map(function ($notification) {
                // Update notification with current product stock if product exists
                if ($notification->product) {
                    $currentStock = $notification->product->getTotalStock();
                    // Update the notification's current_stock attribute
                    $notification->current_stock = $currentStock;
                }
                return $notification;
            });

        return view('admin.inventory', compact(
            'lowStockProducts', 
            'outOfStockProducts', 
            'products',
            'recentNotifications'
        ));
    }

    /**
     * Mark low stock notification as resolved
     */
    public function resolveNotification(Request $request, $id)
    {
        try {
            $notification = \App\Models\LowStockNotification::with('product')->findOrFail($id);
            
            // Check if force flag is set
            $force = $request->input('force', false);
            
            // Get current stock from the product
            $product = $notification->product;
            if ($product) {
                $currentStock = $product->getTotalStock();
                
                // Update notification with current stock
                $notification->current_stock = $currentStock;
                
                // If stock is still low and not forcing, warn the admin
                if ($currentStock <= 5 && !$force) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock is still low. Current stock: ' . $currentStock,
                        'current_stock' => $currentStock,
                        'needs_confirmation' => true,
                        'product_name' => $product->name
                    ]);
                }
            }
            
            // Mark as resolved
            $notification->markAsResolved();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as resolved',
                'current_stock' => $currentStock ?? $notification->current_stock
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove low stock notification
     */
    public function removeNotification(Request $request, $id)
    {
        try {
            $notification = \App\Models\LowStockNotification::findOrFail($id);
            $notification->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove notification: ' . $e->getMessage()
            ], 500);
        }
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
        // Apply filters
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $period = $request->input('period');
        $status = $request->input('status', 'all');
        
        // Determine date range based on period if set
        if ($period && !$dateFrom && !$dateTo) {
            $today = now();
            switch($period) {
                case 'today':
                    $dateFrom = $today->toDateString();
                    $dateTo = $today->toDateString();
                    break;
                case 'week':
                    $dateFrom = $today->subDays(7)->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case 'month':
                    $dateFrom = $today->startOfMonth()->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case 'last_month':
                    $dateFrom = $today->subMonth()->startOfMonth()->toDateString();
                    $dateTo = $today->endOfMonth()->toDateString();
                    break;
                case '3months':
                    $dateFrom = $today->subMonths(3)->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case '6months':
                    $dateFrom = $today->subMonths(6)->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case 'year':
                    $dateFrom = $today->subYear()->toDateString();
                    $dateTo = now()->toDateString();
                    break;
            }
        }
        
        // Build query with filters
        $regularQuery = Order::query();
        $customQuery = \App\Models\CustomDesignOrder::query();
        
        if ($dateFrom) {
            $regularQuery->whereDate('created_at', '>=', $dateFrom);
            $customQuery->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $regularQuery->whereDate('created_at', '<=', $dateTo);
            $customQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        // Status filter
        $statusFilter = ['completed', 'delivered', 'shipped', 'processing'];
        if ($status !== 'all') {
            $statusFilter = [$status];
        }
        
        $regularQuery->whereIn('status', $statusFilter);
        $customQuery->whereIn('status', $statusFilter);
        
        // Sales by month (last 12 months) - PostgreSQL compatible
        $driver = DB::getDriverName();
        $ymExpr = $driver === 'pgsql' ? 'TO_CHAR(created_at, \'YYYY-MM\')' : 'DATE_FORMAT(created_at, "%Y-%m")';

        // Get regular orders sales by month
        $regular_sales_by_month = $regularQuery->clone()->select(
                DB::raw($ymExpr . ' as ym'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        // Get custom design orders sales by month
        $custom_sales_by_month = $customQuery->clone()->select(
                DB::raw($ymExpr . ' as ym'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        // Combine both order types by month
        $all_sales = $regular_sales_by_month->concat($custom_sales_by_month);
        $sales_by_month = $all_sales->groupBy('ym')->map(function ($monthOrders) {
            $totalOrders = $monthOrders->sum('orders_count');
            $totalRevenue = $monthOrders->sum('revenue');
            return (object) [
                'month' => $monthOrders->first()->ym,
                'orders_count' => (int) $totalOrders,
                'revenue' => (float) $totalRevenue,
            ];
        })->sortBy('month')->values();

        // Calculate totals with filters
        $regular_revenue = (float) $regularQuery->clone()->sum('total_amount');
        $custom_revenue = (float) $customQuery->clone()->sum('total_amount');
        $total_revenue = $regular_revenue + $custom_revenue;

        $regular_orders = (int) $regularQuery->clone()->count();
        $custom_orders = (int) $customQuery->clone()->count();
        $total_orders = $regular_orders + $custom_orders;

        $average_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;

        // Best selling products (by quantity)
        $best_sellers = DB::table('order_items')
            ->join('barong_products', 'order_items.product_id', '=', 'barong_products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('product_id', 'barong_products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.total_price) as total_sales'))
            ->whereIn('orders.status', $statusFilter);
            
        if ($dateFrom) {
            $best_sellers->whereDate('orders.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $best_sellers->whereDate('orders.created_at', '<=', $dateTo);
        }
            
        $best_sellers = $best_sellers
            ->groupBy('product_id', 'barong_products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Top customers by total spent (with filters applied)
        $top_customers = User::select('users.id', 'users.name', 'users.email')
            ->selectRaw('COUNT(orders.id) as orders_count')
            ->selectRaw('SUM(orders.total_amount) as total_spent')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereIn('orders.status', $statusFilter)
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('orders.created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('orders.created_at', '<=', $dateTo);
            })
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Custom design orders data
        $custom_orders = $customQuery->clone()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Custom design orders summary
        $custom_orders_summary = [
            'total_custom_orders' => $customQuery->clone()->count(),
            'total_custom_revenue' => $customQuery->clone()->sum('total_amount'),
            'pending_custom_orders' => \App\Models\CustomDesignOrder::where('status', 'pending')->count(),
            'custom_orders_by_fabric' => $customQuery->clone()
                ->select('fabric', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as revenue'))
                ->groupBy('fabric')
                ->orderByDesc('count')
                ->get(),
        ];

        // Weekly sales with filters
        $weeklySalesQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('barong_products', 'order_items.product_id', '=', 'barong_products.id')
            ->select(
                'barong_products.id as product_id',
                'barong_products.name as product_name',
                'barong_products.sku as product_sku',
                DB::raw("TO_CHAR(orders.created_at, 'IYYY-\"W\"IW') as week"),
                DB::raw("DATE_TRUNC('week', orders.created_at)::date as week_start"),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_sales')
            )
            ->whereIn('orders.status', $statusFilter);
            
        if ($dateFrom) {
            $weeklySalesQuery->whereDate('orders.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $weeklySalesQuery->whereDate('orders.created_at', '<=', $dateTo);
        }
        
        // If no date range specified, default to last 3 months
        if (!$dateFrom && !$dateTo) {
            $weeklySalesQuery->where('orders.created_at', '>=', now()->subMonths(3));
        }
            
        $weeklySales = $weeklySalesQuery
            ->groupBy('barong_products.id', 'barong_products.name', 'barong_products.sku', 'week', 'week_start')
            ->orderBy('week', 'desc')
            ->orderBy('total_sales', 'desc')
            ->get();

        $salesReport = [
            'total_revenue' => $total_revenue,
            'total_orders' => $total_orders,
            'average_order_value' => $average_order_value,
            'top_customers' => $top_customers,
            'sales_by_month' => $sales_by_month,
            'best_sellers' => $best_sellers,
            'custom_orders' => $custom_orders,
            'custom_orders_summary' => $custom_orders_summary,
            'weekly_sales' => $weeklySales,
        ];

        return view('admin.reports', compact('salesReport'));
    }

    /**
     * Print-friendly reports view
     */
    public function reportsPrint(Request $request)
    {
        $reportStartDate = now()->subDays(6)->startOfDay();
        $reportEndDate = now()->endOfDay();
        
        // Get daily sales for last 7 days
        $dailySales = [];
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $daysOfWeek[$date->dayOfWeek];
            $dateStr = $date->format('Y-m-d');
            
            $orders = Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
                ->whereDate('created_at', $dateStr)
                ->get();
            
            $dailySales[] = [
                'date' => $dateStr,
                'day_name' => $dayName,
                'revenue' => $orders->sum('total_amount'),
                'orders' => $orders->count(),
            ];
        }
        
        // Build category-by-day sales matrix
        $rawCategoryDaily = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('barong_products', 'order_items.product_id', '=', 'barong_products.id')
            ->leftJoin('categories', 'barong_products.category_id', '=', 'categories.id')
            ->select(
                DB::raw("COALESCE(categories.name, 'Uncategorized') as category_name"),
                DB::raw("DATE(orders.created_at) as sales_date"),
                DB::raw('SUM(order_items.total_price) as day_total')
            )
            ->whereIn('orders.status', ['completed', 'delivered', 'shipped', 'processing'])
            ->whereBetween('orders.created_at', [$reportStartDate, $reportEndDate])
            ->groupBy('category_name', 'sales_date')
            ->get();

        // Initialize matrix with zeros for all categories and days
        $categoryNames = $rawCategoryDaily->pluck('category_name')->unique()->values();
        $productSales = [];
        foreach ($categoryNames as $cat) {
            $productSales[$cat] = [
                'category_name' => $cat,
                'daily' => array_fill(0, 7, 0.0),
                'total_sales' => 0.0,
            ];
        }

        // Helper map from date->day index 0..6
        $dateToIndex = [];
        foreach ($dailySales as $idx => $day) {
            $dateToIndex[$day['date']] = $idx; // 0..6 oldest->newest
        }

        foreach ($rawCategoryDaily as $row) {
            $cat = $row->category_name;
            $date = (string) $row->sales_date;
            if (!isset($productSales[$cat])) {
                $productSales[$cat] = [
                    'category_name' => $cat,
                    'daily' => array_fill(0, 7, 0.0),
                    'total_sales' => 0.0,
                ];
            }
            if (isset($dateToIndex[$date])) {
                $i = $dateToIndex[$date];
                $productSales[$cat]['daily'][$i] += (float) $row->day_total;
                $productSales[$cat]['total_sales'] += (float) $row->day_total;
            }
        }

        // Sort categories by weekly total desc
        uasort($productSales, function ($a, $b) {
            return $b['total_sales'] <=> $a['total_sales'];
        });
        $productSales = array_values($productSales);
        
        $totalRevenue = (float) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->where('created_at', '>=', $reportStartDate)
            ->where('created_at', '<=', $reportEndDate)
            ->sum('total_amount');
        
        $totalOrders = (int) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->where('created_at', '>=', $reportStartDate)
            ->where('created_at', '<=', $reportEndDate)
            ->count();
        
        $monthToDate = (float) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        
        $previousMonth = (float) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');
        
        $yearToDate = (float) Order::whereIn('status', ['completed', 'delivered', 'shipped', 'processing'])
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        
        $salesReport = [
            'report_start_date' => $reportStartDate,
            'report_end_date' => $reportEndDate,
            'daily_sales' => $dailySales,
            'product_sales' => $productSales,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_products' => BarongProduct::count(),
            'total_customers' => User::where('role', '!=', 'admin')->count(),
            'month_to_date' => $monthToDate,
            'previous_month' => $previousMonth,
            'year_to_date' => $yearToDate,
        ];

        return view('admin.reports-print', compact('salesReport'));
    }

    /**
     * Export reports to CSV format
     */
    public function exportReports(Request $request)
    {
        // Apply filters (same as reports function)
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $period = $request->input('period');
        $status = $request->input('status', 'all');
        
        // Determine date range based on period if set
        if ($period && !$dateFrom && !$dateTo) {
            $today = now();
            switch($period) {
                case 'today':
                    $dateFrom = $today->toDateString();
                    $dateTo = $today->toDateString();
                    break;
                case 'week':
                    $dateFrom = $today->subDays(7)->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case 'month':
                    $dateFrom = $today->startOfMonth()->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case 'last_month':
                    $dateFrom = $today->subMonth()->startOfMonth()->toDateString();
                    $dateTo = $today->endOfMonth()->toDateString();
                    break;
                case '3months':
                    $dateFrom = $today->subMonths(3)->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case '6months':
                    $dateFrom = $today->subMonths(6)->toDateString();
                    $dateTo = now()->toDateString();
                    break;
                case 'year':
                    $dateFrom = $today->subYear()->toDateString();
                    $dateTo = now()->toDateString();
                    break;
            }
        }
        
        // Build query with filters
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('barong_products', 'order_items.product_id', '=', 'barong_products.id')
            ->select(
                'order_items.order_id',
                'barong_products.sku as item_no',
                'barong_products.name as item_name',
                'barong_products.description as item_description',
                'order_items.unit_price as price',
                'order_items.quantity as qty',
                'order_items.total_price as amount'
            );
        
        // Apply status filter
        $statusFilter = ['completed', 'delivered', 'shipped', 'processing'];
        if ($status !== 'all') {
            $statusFilter = [$status];
        }
        $query->whereIn('orders.status', $statusFilter);
        
        // Apply date filters
        if ($dateFrom) {
            $query->whereDate('orders.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('orders.created_at', '<=', $dateTo);
        }
        
        // If no date range specified, default to last 3 months
        if (!$dateFrom && !$dateTo) {
            $query->where('orders.created_at', '>=', now()->subMonths(3));
        }
        
        $salesData = $query->orderBy('orders.created_at', 'desc')->get();
        
        // Prepare CSV data
        $filename = 'sales_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() use ($salesData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 to support special characters
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers with proper formatting
            fputcsv($file, [
                'ITEM NO',
                'ITEM NAME',
                'ITEM DESCRIPTION',
                'PRICE',
                'QTY',
                'AMOUNT',
                'TAX RATE',
                'TAX',
                'TOTAL'
            ]);
            
            $grandTotal = 0;
            
            foreach ($salesData as $item) {
                // Calculate tax (assuming 12% VAT for Philippines)
                $taxRate = 12; // 12% VAT
                $taxAmount = $item->amount * ($taxRate / 100);
                $total = $item->amount + $taxAmount;
                $grandTotal += $total;
                
                // Truncate description if too long
                $description = substr($item->item_description ?? '', 0, 50);
                
                fputcsv($file, [
                    $item->item_no ?? 'N/A',
                    $item->item_name ?? 'Unknown',
                    $description,
                    number_format($item->price, 2),
                    $item->qty,
                    number_format($item->amount, 2),
                    $taxRate . '%',
                    number_format($taxAmount, 2),
                    number_format($total, 2)
                ]);
            }
            
            // Empty row
            fputcsv($file, [' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ']);
            
            // Grand total row
            fputcsv($file, [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'GRAND TOTAL:',
                number_format($grandTotal, 2)
            ]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
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
        $order->subtotal = (float)($product->current_price * 2);
        $order->discount = 0.0;
        $order->shipping_fee = 0.0;
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
            'unit_price' => $product->current_price,
            'total_price' => $product->current_price * 2,
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

        $categories = Category::all();
        if ($categories->isEmpty()) {
            return back()->with('error', 'Please ensure at least one category exists.');
        }

        $names = [
            'Classic Barong', 'Modern Barong', 'Heritage Barong', 'Wedding Barong', 'Casual Barong',
            'Handwoven Barong', 'Slim Fit Barong', 'Pi+�a Barong', 'Cocoon Barong', 'Embroidered Barong'
        ];

        $faker = FakerFactory::create();
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
        $faker = FakerFactory::create();

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
            $order->discount = 0.0;
            $order->shipping_fee = 0.0;
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

            $order->subtotal = (float)$subtotal;
            $order->total_amount = (float)$subtotal;
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

    /**
     * Update Veriff document status (temporary utility)
     */
    public function updateVeriffStatus(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'status' => 'required|in:approved,pending,rejected',
        ]);

        $document = \App\Models\IdDocument::where('veriff_session_id', $request->session_id)->first();

        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        $oldStatus = $document->status;
        $document->status = $request->status;
        $document->save();

        return response()->json([
            'success' => true,
            'message' => "Status updated from '{$oldStatus}' to '{$request->status}'",
            'document' => [
                'id' => $document->id,
                'user_id' => $document->user_id,
                'session_id' => $document->veriff_session_id,
                'status' => $document->status,
            ]
        ]);
    }

    /**
     * Sync Veriff status from Veriff API
     */
    public function syncVeriffStatus(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $document = \App\Models\IdDocument::where('veriff_session_id', $request->session_id)->first();

        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        $apiKey = Config::get('services.veriff.api_key');
        $apiSecret = Config::get('services.veriff.secret_key');

        if (!$apiKey || !$apiSecret) {
            return response()->json(['error' => 'Veriff API credentials not configured'], 500);
        }

        $sessionId = $request->input('session_id');

        try {
            // Generate HMAC signature for GET request
            // For GET requests, Veriff requires signing the session ID
            $signature = hash_hmac('sha256', $sessionId, $apiSecret);

            // Fetch verification decision from Veriff API using the /decision endpoint
            $response = Http::withHeaders([
                'X-Auth-Client' => $apiKey,
                'X-Signature' => $signature,
            ])->get("https://api.veriff.me/v1/sessions/{$sessionId}/decision");

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to fetch status from Veriff',
                    'details' => $response->json(),
                    'status_code' => $response->status(),
                ], $response->status());
            }

            $veriffData = $response->json('verification');
            
            if (!$veriffData || !isset($veriffData['status'])) {
                return response()->json([
                    'error' => 'Invalid response from Veriff API',
                    'response' => $response->json(),
                ], 400);
            }

            $veriffStatus = $veriffData['status'];
            $oldStatus = $document->status;

            // Map Veriff status to our status
            $newStatus = $this->mapVeriffStatus($veriffStatus);
            
            $document->status = $newStatus;
            $document->save();

            return response()->json([
                'success' => true,
                'message' => "Status synced from Veriff. Changed from '{$oldStatus}' to '{$newStatus}'",
                'veriff_status' => $veriffStatus,
                'document' => [
                    'id' => $document->id,
                    'user_id' => $document->user_id,
                    'session_id' => $document->veriff_session_id,
                    'status' => $document->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to sync status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Map Veriff status to our internal status
     */
    private function mapVeriffStatus(string $veriffStatus): string
    {
        $status = strtolower(trim($veriffStatus));

        switch ($status) {
            case 'approved':
                return 'approved';
            
            case 'declined':
            case 'rejected':
                return 'rejected';
            
            case 'resubmission_requested':
            case 'expired':
            case 'abandoned':
            case 'pending':
            case 'created':
            case 'submitted':
            default:
                return 'pending';
        }
    }
}

