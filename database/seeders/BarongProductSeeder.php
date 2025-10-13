<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BarongProduct;
use App\Models\Brand;
use App\Models\Category;

class BarongProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get brand and category IDs
        $brand3Migs = Brand::where('slug', '3migs')->first();
        $traditionalCategory = Category::where('slug', 'traditional-barong')->first();
        $modernCategory = Category::where('slug', 'modern-barong')->first();

        $barongProducts = [
            [
                'name' => 'Classic Traditional Barong',
                'type' => 'Traditional Barong',
                'description' => 'A timeless traditional Filipino barong with intricate embroidery and classic cut. Perfect for formal occasions and cultural events.',
                'brand_id' => $brand3Migs->id,
                'category_id' => $traditionalCategory->id,
                'base_price' => 2999.99,
                'special_price' => 2499.99,
                'stock' => 50,
                'images' => ['barong-products/traditional-barong-1.jpg'],
                'cover_image' => 'barong-products/traditional-barong-1.jpg',
                'fabric' => ['Jusi', 'Pinya'],
                'embroidery_style' => ['Hand-Embroidered', 'U-Shape'],
                'colors' => ['White', 'Cream'],
                'sleeve_type' => 'Long Sleeve',
                'is_available' => true,
                'is_featured' => true,
                'has_variations' => true,
                'variations' => [
                    ['size' => 'S', 'color' => 'White', 'price' => 2499.99, 'stock' => 10],
                    ['size' => 'M', 'color' => 'White', 'price' => 2499.99, 'stock' => 15],
                    ['size' => 'L', 'color' => 'White', 'price' => 2499.99, 'stock' => 12],
                    ['size' => 'XL', 'color' => 'White', 'price' => 2499.99, 'stock' => 8],
                    ['size' => 'S', 'color' => 'Cream', 'price' => 2499.99, 'stock' => 5],
                ],
            ],
            [
                'name' => 'Modern Slim Fit Barong',
                'type' => 'Modern Barong',
                'description' => 'Contemporary barong with modern cuts and minimalist design. Perfect for business and casual events.',
                'brand_id' => $brand3Migs->id,
                'category_id' => $modernCategory->id,
                'base_price' => 2299.99,
                'special_price' => null,
                'stock' => 40,
                'images' => ['barong-products/modern-barong-1.jpg'],
                'cover_image' => 'barong-products/modern-barong-1.jpg',
                'fabric' => ['Organza', 'Ramie'],
                'embroidery_style' => ['Computerized'],
                'colors' => ['White', 'Navy Blue', 'Black'],
                'sleeve_type' => 'Long Sleeve',
                'is_available' => true,
                'is_featured' => false,
                'has_variations' => true,
                'variations' => [
                    ['size' => 'S', 'color' => 'White', 'price' => 2299.99, 'stock' => 8],
                    ['size' => 'M', 'color' => 'White', 'price' => 2299.99, 'stock' => 12],
                    ['size' => 'L', 'color' => 'White', 'price' => 2299.99, 'stock' => 10],
                    ['size' => 'XL', 'color' => 'White', 'price' => 2299.99, 'stock' => 6],
                    ['size' => 'M', 'color' => 'Navy Blue', 'price' => 2299.99, 'stock' => 4],
                ],
            ],
            [
                'name' => 'Premium Wedding Barong',
                'type' => 'Traditional Barong',
                'description' => 'Luxury wedding barong with hand-embroidered details and premium jusi fabric. Perfect for your special day.',
                'brand_id' => $brand3Migs->id,
                'category_id' => $traditionalCategory->id,
                'base_price' => 4999.99,
                'special_price' => 4499.99,
                'stock' => 25,
                'images' => ['barong-products/wedding-barong-1.jpg'],
                'cover_image' => 'barong-products/wedding-barong-1.jpg',
                'fabric' => ['Jusi', 'Silk Cocoon'],
                'embroidery_style' => ['Hand-Embroidered', 'Full Front'],
                'colors' => ['White', 'Cream', 'Gold'],
                'sleeve_type' => 'Long Sleeve',
                'is_available' => true,
                'is_featured' => true,
                'has_variations' => false,
                'variations' => null,
            ],
            [
                'name' => 'Kids Traditional Barong',
                'type' => 'Barong for Kids',
                'description' => 'Adorable traditional barong designed specifically for children. Perfect for cultural events and family gatherings.',
                'brand_id' => $brand3Migs->id,
                'category_id' => $traditionalCategory->id,
                'base_price' => 1299.99,
                'special_price' => null,
                'stock' => 30,
                'images' => ['barong-products/kids-barong-1.jpg'],
                'cover_image' => 'barong-products/kids-barong-1.jpg',
                'fabric' => ['Pinya'],
                'embroidery_style' => ['Computerized'],
                'colors' => ['White', 'Cream'],
                'sleeve_type' => 'Long Sleeve',
                'is_available' => true,
                'is_featured' => false,
                'has_variations' => true,
                'variations' => [
                    ['size' => 'XS', 'color' => 'White', 'price' => 1299.99, 'stock' => 8],
                    ['size' => 'S', 'color' => 'White', 'price' => 1299.99, 'stock' => 10],
                    ['size' => 'M', 'color' => 'White', 'price' => 1299.99, 'stock' => 7],
                    ['size' => 'L', 'color' => 'White', 'price' => 1299.99, 'stock' => 5],
                ],
            ],
        ];

        foreach ($barongProducts as $product) {
            BarongProduct::create($product);
        }
    }
}
