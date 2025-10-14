<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BarongProduct extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'brand_id',
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
        'variations',
        'is_available',
        'is_featured',
        'has_variations',
        'sku',
        'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'fabric' => 'array',
        'embroidery_style' => 'array',
        'colors' => 'array',
        'design_details' => 'array',
        'size_stocks' => 'array',
        'variations' => 'array',
        'base_price' => 'decimal:2',
        'special_price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'has_variations' => 'boolean',
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
        // Check if product has variations
        if ($product->has_variations && $product->variations) {
            // For products with variations, check if any variation has stock
            foreach ($product->variations as $variation) {
                if (isset($variation['stock']) && $variation['stock'] > 0) {
                    return true;
                }
            }
            return false;
        }
        
        // Check size stocks
        if ($product->size_stocks && is_array($product->size_stocks)) {
            foreach ($product->size_stocks as $stock) {
                if ($stock > 0) {
                    return true;
                }
            }
            return false;
        }
        
        // Check main stock
        return $product->stock > 0;
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
            
            // Check if it's a local file
            if (file_exists(storage_path('app/public/' . $this->cover_image))) {
                return asset('storage/' . $this->cover_image);
            }
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
                
                // Check if it's a local file
                if (file_exists(storage_path('app/public/' . $image))) {
                    return asset('storage/' . $image);
                }
                
                return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAxMkMxNi42ODYzIDEyIDE0IDE0LjY4NjMgMTQgMThDMTQgMjEuMzEzNyAxNi42ODYzIDI0IDIwIDI0QzIzLjMxMzcgMjQgMjYgMjEuMzEzNyAyNiAxOEMyNiAxNC42ODYzIDIzLjMxMzcgMTIgMjAgMTJaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xMiAyOEMxMiAyNi44OTU0IDEyLjg5NTQgMjYgMTQgMjZIMjZDMjcuMTA0NiAyNiAyOCAyNi44OTU0IDI4IDI4VjMwSDI4VjI4SDEyVjI4WiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K';
            }, $this->images);
        }
        return ['data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAxMkMxNi42ODYzIDEyIDE0IDE0LjY4NjMgMTQgMThDMTQgMjEuMzEzNyAxNi42ODYzIDI0IDIwIDI0QzIzLjMxMzcgMjQgMjYgMjEuMzEzNyAyNiAxOEMyNiAxNC42ODYzIDIzLjMxMzcgMTIgMjAgMTJaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xMiAyOEMxMiAyNi44OTU0IDEyLjg5NTQgMjYgMTQgMjZIMjZDMjcuMTA0NiAyNiAyOCAyNi44OTU0IDI4IDI4VjMwSDI4VjI4SDEyVjI4WiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K'];
    }

    /**
     * Get total stock including variations
     */
    public function getTotalStockAttribute()
    {
        // Delegate to unified calculator to keep logic in one place
        return $this->getTotalStock();
    }

    /**
     * Programmatic method to get total stock across variations or sizes
     */
    public function getTotalStock(): int
    {
        // If product has variations, sum their stock
        if ($this->has_variations && !empty($this->variations) && is_array($this->variations)) {
            $variationStocks = array_column($this->variations, 'stock');
            $variationTotal = array_sum(array_map('intval', $variationStocks));
            return (int) $variationTotal;
        }

        // If product uses size-based stocks, sum all sizes
        if (!empty($this->size_stocks) && is_array($this->size_stocks)) {
            $sizeTotal = array_sum(array_map('intval', $this->size_stocks));
            return (int) $sizeTotal;
        }

        // Fallback to simple stock column
        return (int) ($this->stock ?? 0);
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
}
