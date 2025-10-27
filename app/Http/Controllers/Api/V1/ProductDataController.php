<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BarongProduct;
use Illuminate\Http\Request;

class ProductDataController extends Controller
{
    /**
     * Get fresh product data by slug
     */
    public function getProductBySlug($slug)
    {
        try {
            $product = BarongProduct::with(['category'])
                ->where('slug', $slug)
                ->available()
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'base_price' => $product->base_price,
                    'special_price' => $product->special_price,
                    'current_price' => $product->current_price,
                    'size_stocks' => $product->size_stocks ?? [],
                    'is_available' => $product->is_available,
                    'is_featured' => $product->is_featured,
                    'images' => $product->images ?? [],
                    'cover_image_url' => $product->cover_image_url,
                    'category' => $product->category,
                    'updated_at' => $product->updated_at->toISOString(),
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product size stocks only
     */
    public function getProductSizeStocks($slug)
    {
        try {
            $product = BarongProduct::where('slug', $slug)
                ->select('id', 'slug', 'size_stocks', 'updated_at')
                ->first();

            if (!$product) {
                \Log::warning('Product not found for size-stocks request', [
                    'slug' => $slug,
                    'request_url' => request()->url(),
                    'user_agent' => request()->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                    'error' => 'Product with slug "' . $slug . '" does not exist'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'size_stocks' => $product->size_stocks ?? [],
                'updated_at' => $product->updated_at->toISOString(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching size stocks', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching size stocks: ' . $e->getMessage()
            ], 500);
        }
    }
}