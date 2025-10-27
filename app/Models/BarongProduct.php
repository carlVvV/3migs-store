<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BarongProduct extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'category_id',
        'images',
        'cover_image',
        'video_url',
        'fabric',
        'embroidery_style',
        'colors',
        'sleeve_type',
        'collar_type',
        'design_details',
        'base_price',
        'special_price',
        'stock',
        'size_stocks',
        'color_stocks',
        'variations',
        'is_available',
        'is_featured',
        'is_new_arrival',
        'has_variations',
        'sku',
        'sort_order',
        'sales_count',
        'monthly_sales',
        'last_sale_at',
    ];

    protected $casts = [
        'images' => 'array',
        'fabric' => 'array',
        'embroidery_style' => 'array',
        'colors' => 'array',
        'collar_type' => 'array',
        'design_details' => 'array',
        'size_stocks' => 'array',
        'color_stocks' => 'array',
        'variations' => 'array',
        'base_price' => 'decimal:2',
        'special_price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'has_variations' => 'boolean',
        'last_sale_at' => 'datetime',
    ];

    /**
     * Mutator to ensure size_stocks values are integers
     */
    public function setSizeStocksAttribute($value)
    {
        if (is_array($value)) {
            // Convert all values to integers
            $this->attributes['size_stocks'] = json_encode(array_map('intval', $value));
        } else {
            $this->attributes['size_stocks'] = $value;
        }
    }

    /**
     * Boot method to generate slug and SKU automatically
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'BRG-' . strtoupper(Str::random(8));
            }
            
            // Auto-set availability based on stock
            $product->is_available = static::calculateAvailability($product);
        });
        
        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
            
            // Auto-update availability based on stock changes
            if ($product->isDirty(['stock', 'size_stocks', 'variations'])) {
                $product->is_available = static::calculateAvailability($product);
            }
        });
    }

    /**
     * Calculate availability based on stock levels
     */
    protected static function calculateAvailability($product)
    {
        // Prefer size-based stocks when present
        if ($product->size_stocks && is_array($product->size_stocks)) {
            foreach ($product->size_stocks as $stock) {
                if (intval($stock) > 0) {
                    return true;
                }
            }
            return false;
        }

        // Otherwise fall back to variations
        if ($product->has_variations && $product->variations) {
            foreach ($product->variations as $variation) {
                if (isset($variation['stock']) && intval($variation['stock']) > 0) {
                    return true;
                }
            }
            return false;
        }

        // Finally, fallback to simple stock
        return intval($product->stock) > 0;
    }

    /**
     * Generate a unique slug for the product
     */
    protected static function generateUniqueSlug($name, $id = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // Check if slug exists (excluding current product if updating)
        $query = static::where('slug', $slug);
        if ($id) {
            $query->where('id', '!=', $id);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $query = static::where('slug', $slug);
            if ($id) {
                $query->where('id', '!=', $id);
            }
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the brand that owns the barong product
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the category that owns the barong product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the reviews for this product
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    /**
     * Get approved reviews for this product
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class, 'product_id')->where('is_approved', true);
    }

    /**
     * Get the average rating for this product
     */
    public function getAverageRatingAttribute()
    {
        // Use the stored average_rating column instead of calculating dynamically
        return $this->attributes['average_rating'] ?? 0;
    }

    /**
     * Get the review count for this product
     */
    public function getReviewCountAttribute()
    {
        // Use the stored review_count column instead of calculating dynamically
        return $this->attributes['review_count'] ?? 0;
    }

    /**
     * Get rating distribution for this product
     */
    public function getRatingDistribution()
    {
        return $this->approvedReviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();
    }

    /**
     * Get the current price (special price if available, otherwise base price)
     */
    public function getCurrentPriceAttribute()
    {
        return $this->special_price ?? $this->base_price;
    }

    /**
     * Check if the product is on sale
     */
    public function getIsOnSaleAttribute()
    {
        return !is_null($this->special_price) && $this->special_price < $this->base_price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->is_on_sale) {
            return round((($this->base_price - $this->special_price) / $this->base_price) * 100);
        }
        return 0;
    }

    /**
     * Get the cover image URL
     */
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            // Check if it's a Cloudinary URL
            if (strpos($this->cover_image, 'res.cloudinary.com') !== false) {
                return $this->cover_image;
            }
            
            // Return asset URL without checking file existence (performance optimization)
            return asset('storage/' . $this->cover_image);
        }
        
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAxMkMxNi42ODYzIDEyIDE0IDE0LjY4NjMgMTQgMThDMTQgMjEuMzEzNyAxNi42ODYzIDI0IDIwIDI0QzIzLjMxMzcgMjQgMjYgMjEuMzEzNyAyNiAxOEMyNiAxNC42ODYzIDIzLjMxMzcgMTIgMjAgMTJaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xMiAyOEMxMiAyNi44OTU0IDEyLjg5NTQgMjYgMTQgMjZIMjZDMjcuMTA0NiAyNiAyOCAyNi44OTU0IDI4IDI4VjMwSDI4VjI4SDEyVjI4WiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K';
    }

    /**
     * Get all image URLs
     */
    public function getImageUrlsAttribute()
    {
        if ($this->images && is_array($this->images)) {
            return array_map(function ($image) {
                // Check if it's a Cloudinary URL
                if (strpos($image, 'res.cloudinary.com') !== false) {
                    return $image;
                }
                
                // Return asset URL without checking file existence (performance optimization)
                return asset('storage/' . $image);
            }, $this->images);
        }
        return ['data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAxMkMxNi42ODYzIDEyIDE0IDE0LjY4NjMgMTQgMThDMTQgMjEuMzEzNyAxNi42ODYzIDI0IDIwIDI0QzIzLjMxMzcgMjQgMjYgMjEuMzEzNyAyNiAxOEMyNiAxNC42ODYzIDIzLjMxMzcgMTIgMjAgMTJaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xMiAyOEMxMiAyNi44OTU0IDEyLjg5NTQgMjYgMTQgMjZIMjZDMjcuMTA0NiAyNiAyOCAyNi44OTU0IDI4IDI4VjMwSDI4VjI4SDEyVjI4WiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K'];
    }

    /**
     * Get total stock including variations
     */
    public function getTotalStockAttribute()
    {
        // For performance, use stored stock column directly
        // If you need calculated stock, call getTotalStock() method directly
        return (int) ($this->attributes['stock'] ?? 0);
    }

    /**
     * Programmatic method to get total stock across variations or sizes
     */
    public function getTotalStock(): int
    {
        // Prefer color_stocks when present (size + color specific)
        if (!empty($this->color_stocks) && is_array($this->color_stocks)) {
            $totalStock = 0;
            foreach ($this->color_stocks as $size => $colors) {
                if (is_array($colors)) {
                    foreach ($colors as $color => $qty) {
                        $totalStock += intval($qty);
                    }
                }
            }
            return (int) $totalStock;
        }
        
        // Prefer size-based stocks when present
        if (!empty($this->size_stocks) && is_array($this->size_stocks)) {
            $sizeTotal = array_sum(array_map('intval', $this->size_stocks));
            return (int) $sizeTotal;
        }

        // Otherwise sum variation stocks
        if ($this->has_variations && !empty($this->variations) && is_array($this->variations)) {
            $variationStocks = array_column($this->variations, 'stock');
            $variationTotal = array_sum(array_map('intval', $variationStocks));
            return (int) $variationTotal;
        }

        // Fallback to simple stock column
        return (int) ($this->stock ?? 0);
    }

    /**
     * Get available colors and sizes from color_stocks
     */
    public function getAvailableColorsAndSizes(): array
    {
        if (empty($this->color_stocks) || !is_array($this->color_stocks)) {
            return [
                'colors' => [],
                'sizes' => [],
                'color_stocks' => []
            ];
        }

        $sizes = [];
        $colors = [];
        $colorStocks = $this->color_stocks;

        foreach ($colorStocks as $size => $colorData) {
            if (!in_array($size, $sizes)) {
                $sizes[] = $size;
            }
            
            if (is_array($colorData)) {
                foreach ($colorData as $color => $qty) {
                    if (intval($qty) > 0 && !in_array($color, $colors)) {
                        $colors[] = $color;
                    }
                }
            }
        }

        return [
            'colors' => $colors,
            'sizes' => $sizes,
            'color_stocks' => $colorStocks
        ];
    }

    /**
     * Get available colors for a specific size
     */
    public function getAvailableColorsForSize(string $size): array
    {
        if (empty($this->color_stocks) || !is_array($this->color_stocks)) {
            return [];
        }

        $colors = [];
        if (isset($this->color_stocks[$size]) && is_array($this->color_stocks[$size])) {
            foreach ($this->color_stocks[$size] as $color => $qty) {
                if (intval($qty) > 0) {
                    $colors[] = $color;
                }
            }
        }

        return $colors;
    }

    /**
     * Get available sizes
     */
    public function getAvailableSizes(): array
    {
        if (empty($this->color_stocks) || !is_array($this->color_stocks)) {
            return [];
        }

        return array_keys($this->color_stocks);
    }

    /**
     * Get stock quantity for a specific size and color
     */
    public function getStockForSizeAndColor(string $size, string $color): int
    {
        if (empty($this->color_stocks) || !is_array($this->color_stocks)) {
            return 0;
        }

        if (isset($this->color_stocks[$size][$color])) {
            return intval($this->color_stocks[$size][$color]);
        }

        return 0;
    }

    /**
     * Scope for available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for products by brand
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Get available sizes from variations
     */
    public function getAvailableSizesAttribute()
    {
        if ($this->has_variations && $this->variations) {
            return array_unique(array_column($this->variations, 'size'));
        }
        return [];
    }

    /**
     * Get available colors from variations
     */
    public function getAvailableColorsAttribute()
    {
        if ($this->has_variations && $this->variations) {
            return array_unique(array_column($this->variations, 'color'));
        }
        return $this->colors ?? [];
    }

    /**
     * Get stock for a specific size
     */
    public function getStockForSize($size)
    {
        $sizeStocks = $this->size_stocks ?? [];
        return $sizeStocks[$size] ?? 0;
    }

    /**
     * Check if a specific size is available
     */
    public function isSizeAvailable($size)
    {
        return $this->getStockForSize($size) > 0;
    }

    /**
     * Get available sizes with stock
     */
    public function getAvailableSizesWithStock()
    {
        $sizeStocks = $this->size_stocks ?? [];
        $availableSizes = [];
        
        foreach ($sizeStocks as $size => $stock) {
            if ($stock > 0) {
                $availableSizes[$size] = $stock;
            }
        }
        
        return $availableSizes;
    }

    /**
     * Update availability based on current stock levels
     */
    public function updateAvailability()
    {
        $this->is_available = static::calculateAvailability($this);
        $this->save();
        return $this;
    }

    /**
     * Static method to update availability for all products
     */
    public static function updateAllAvailability()
    {
        $products = static::all();
        $updated = 0;
        
        foreach ($products as $product) {
            $oldAvailability = $product->is_available;
            $newAvailability = static::calculateAvailability($product);
            
            if ($oldAvailability !== $newAvailability) {
                $product->is_available = $newAvailability;
                $product->save();
                $updated++;
            }
        }
        
        return $updated;
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock($threshold = 5)
    {
        return $this->getTotalStock() <= $threshold;
    }

    /**
     * Get low stock products
     */
    public static function getLowStockProducts($threshold = 5)
    {
        return static::where('is_available', true)
            ->get()
            ->filter(function($product) use ($threshold) {
                return $product->getTotalStock() <= $threshold;
            });
    }

    /**
     * Get out of stock products
     */
    public static function getOutOfStockProducts()
    {
        // First, get products where stock = 0 (simple stock)
        $outOfStockProducts = static::where('stock', 0)->get();
        
        // Also check products with size_stocks or variations
        $productsWithComplexStock = static::where(function($query) {
            $query->whereNotNull('size_stocks')
                  ->orWhere(function($q) {
                      $q->where('has_variations', true)
                        ->whereNotNull('variations');
                  });
        })->get();
        
        // Filter complex stock products to find those with zero total stock
        foreach ($productsWithComplexStock as $product) {
            if ($product->getTotalStock() == 0) {
                $outOfStockProducts->push($product);
            }
        }
        
        return $outOfStockProducts;
    }

    /**
     * Check if stock change triggers low stock alert
     */
    public function checkLowStockAlert($oldStock = null)
    {
        $currentStock = $this->getTotalStock();
        $threshold = 5;
        
        // If current stock is at or below threshold, trigger alert
        if ($currentStock <= $threshold) {
            $this->triggerLowStockNotification($oldStock, $currentStock);
        }
    }

    /**
     * Trigger low stock notification
     */
    protected function triggerLowStockNotification($oldStock, $currentStock)
    {
        // Log the low stock event
        \Log::warning('Low Stock Alert', [
            'product_id' => $this->id,
            'product_name' => $this->name,
            'product_sku' => $this->sku,
            'old_stock' => $oldStock,
            'current_stock' => $currentStock,
            'threshold' => 5,
            'timestamp' => now()
        ]);

        // Store notification in database for admin dashboard
        $this->createLowStockNotification($currentStock);
    }

    /**
     * Create low stock notification record
     */
    protected function createLowStockNotification($currentStock)
    {
        // Check if notification already exists for this product
        $existingNotification = \App\Models\LowStockNotification::where('product_id', $this->id)
            ->where('is_resolved', false)
            ->first();

        if (!$existingNotification) {
            \App\Models\LowStockNotification::create([
                'product_id' => $this->id,
                'product_name' => $this->name,
                'product_sku' => $this->sku,
                'current_stock' => $currentStock,
                'threshold' => 5,
                'is_resolved' => false,
                'notified_at' => now()
            ]);
        } else {
            // Update existing notification with new stock level
            $existingNotification->update([
                'current_stock' => $currentStock,
                'notified_at' => now()
            ]);
        }
    }

    /**
     * Get all deleted products
     */
    public static function getDeletedProducts()
    {
        return static::onlyTrashed()->with(['category', 'brand'])->get();
    }

    /**
     * Restore a soft deleted product
     */
    public function restoreProduct()
    {
        $this->restore();
        
        // Log the restoration
        \Log::info('Product restored', [
            'product_id' => $this->id,
            'product_name' => $this->name,
            'product_sku' => $this->sku,
            'restored_at' => now(),
            'restored_by' => auth()->id()
        ]);
        
        return $this;
    }

    /**
     * Permanently delete a product
     */
    public function forceDeleteProduct()
    {
        $productData = [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'deleted_at' => $this->deleted_at
        ];
        
        $this->forceDelete();
        
        // Log the permanent deletion
        \Log::warning('Product permanently deleted', [
            'product_data' => $productData,
            'deleted_at' => now(),
            'deleted_by' => auth()->id()
        ]);
        
        return true;
    }

    /**
     * Check if product is soft deleted
     */
    public function isDeleted()
    {
        return $this->trashed();
    }

    /**
     * Get best selling products
     */
    public static function getBestSellingProducts($limit = 8, $period = 'monthly')
    {
        $query = static::where('is_available', true);
        
        if ($period === 'monthly') {
            $query->orderBy('monthly_sales', 'desc');
        } else {
            $query->orderBy('sales_count', 'desc');
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Get best selling products for this month
     */
    public static function getBestSellingThisMonth($limit = 8)
    {
        return static::getBestSellingProducts($limit, 'monthly');
    }

    /**
     * Increment sales count when product is sold
     */
    public function incrementSales($quantity = 1)
    {
        $this->increment('sales_count', $quantity);
        $this->increment('monthly_sales', $quantity);
        $this->update(['last_sale_at' => now()]);
    }

    /**
     * Reset monthly sales (should be called monthly)
     */
    public static function resetMonthlySales()
    {
        static::query()->update(['monthly_sales' => 0]);
    }
}
