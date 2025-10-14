<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $traditionalBarongCategory = Category::where('slug', 'traditional-barong')->first();
        $modernBarongCategory = Category::where('slug', 'modern-barong')->first();
        $weddingBarongCategory = Category::where('slug', 'wedding-barong')->first();
        $formalBarongCategory = Category::where('slug', 'formal-barong')->first();
        $casualBarongCategory = Category::where('slug', 'casual-barong')->first();

        $products = [
            [
                'name' => 'Traditional Jusilyn Barong',
                'slug' => 'traditional-jusilyn-barong',
                'description' => 'Classic traditional Filipino barong made from premium Jusilyn fabric with intricate hand embroidery.',
                'short_description' => 'Premium Jusilyn barong with traditional embroidery',
                'price' => 2500.00,
                'sale_price' => 2200.00,
                'sku' => 'TBJ-001',
                'stock_quantity' => 50,
                'manage_stock' => true,
                'in_stock' => true,
                'product_type' => 'simple',
                'is_featured' => true,
                'is_new_arrival' => false,
                'is_new_design' => false,
                'images' => json_encode(['images/products/traditional-jusilyn-barong-1.jpg', 'images/products/traditional-jusilyn-barong-2.jpg']),
                'attributes' => json_encode([
                    'fabric' => 'Jusilyn',
                    'color' => 'White',
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL']
                ]),
                'variants' => null,
                'weight' => 0.5,
                'dimensions' => '30x20x2',
                'care_instructions' => 'Dry clean only. Do not bleach.',
                'size_guide' => 'Please refer to our size chart for accurate measurements.',
                'is_active' => true,
                'sort_order' => 1,
                'category_id' => $traditionalBarongCategory->id,
            ],
            [
                'name' => 'Modern Hugo Boss Barong',
                'slug' => 'modern-hugo-boss-barong',
                'description' => 'Contemporary barong design with modern cuts and Hugo Boss fabric for a sophisticated look.',
                'short_description' => 'Modern barong with Hugo Boss fabric',
                'price' => 1800.00,
                'sale_price' => null,
                'sku' => 'MBH-001',
                'stock_quantity' => 30,
                'manage_stock' => true,
                'in_stock' => true,
                'product_type' => 'simple',
                'is_featured' => false,
                'is_new_arrival' => true,
                'is_new_design' => true,
                'images' => json_encode(['images/products/modern-hugo-boss-barong-1.jpg']),
                'attributes' => json_encode([
                    'fabric' => 'Hugo Boss',
                    'color' => 'Cream',
                    'sizes' => ['S', 'M', 'L', 'XL']
                ]),
                'variants' => null,
                'weight' => 0.4,
                'dimensions' => '28x18x2',
                'care_instructions' => 'Machine wash cold. Hang dry.',
                'size_guide' => 'Please refer to our size chart for accurate measurements.',
                'is_active' => true,
                'sort_order' => 2,
                'category_id' => $modernBarongCategory->id,
            ],
            [
                'name' => 'Elegant Wedding Barong',
                'slug' => 'elegant-wedding-barong',
                'description' => 'Stunning wedding barong with detailed embroidery perfect for your special day.',
                'short_description' => 'Beautiful wedding barong with detailed embroidery',
                'price' => 3500.00,
                'sale_price' => 3200.00,
                'sku' => 'WB-001',
                'stock_quantity' => 20,
                'manage_stock' => true,
                'in_stock' => true,
                'product_type' => 'simple',
                'is_featured' => true,
                'is_new_arrival' => false,
                'is_new_design' => false,
                'images' => json_encode(['images/products/elegant-wedding-barong-1.jpg', 'images/products/elegant-wedding-barong-2.jpg']),
                'attributes' => json_encode([
                    'fabric' => 'Piña Cocoon',
                    'color' => 'Ivory',
                    'sizes' => ['M', 'L', 'XL', 'XXL']
                ]),
                'variants' => null,
                'weight' => 0.6,
                'dimensions' => '32x22x2',
                'care_instructions' => 'Dry clean only. Handle with care.',
                'size_guide' => 'Please refer to our size chart for accurate measurements.',
                'is_active' => true,
                'sort_order' => 3,
                'category_id' => $weddingBarongCategory->id,
            ],
            [
                'name' => 'Professional Formal Barong',
                'slug' => 'professional-formal-barong',
                'description' => 'Professional barong perfect for business meetings and formal events.',
                'short_description' => 'Professional barong for formal occasions',
                'price' => 2000.00,
                'sale_price' => null,
                'sku' => 'FB-001',
                'stock_quantity' => 40,
                'manage_stock' => true,
                'in_stock' => true,
                'product_type' => 'simple',
                'is_featured' => false,
                'is_new_arrival' => false,
                'is_new_design' => false,
                'images' => json_encode(['images/products/professional-formal-barong-1.jpg']),
                'attributes' => json_encode([
                    'fabric' => 'Gusot Mayaman',
                    'color' => 'White',
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL']
                ]),
                'variants' => null,
                'weight' => 0.5,
                'dimensions' => '30x20x2',
                'care_instructions' => 'Dry clean only.',
                'size_guide' => 'Please refer to our size chart for accurate measurements.',
                'is_active' => true,
                'sort_order' => 4,
                'category_id' => $formalBarongCategory->id,
            ],
            [
                'name' => 'Comfortable Casual Barong',
                'slug' => 'comfortable-casual-barong',
                'description' => 'Comfortable casual barong perfect for everyday wear and relaxed occasions.',
                'short_description' => 'Comfortable barong for casual wear',
                'price' => 1200.00,
                'sale_price' => 1000.00,
                'sku' => 'CB-001',
                'stock_quantity' => 60,
                'manage_stock' => true,
                'in_stock' => true,
                'product_type' => 'simple',
                'is_featured' => false,
                'is_new_arrival' => true,
                'is_new_design' => false,
                'images' => json_encode(['images/products/comfortable-casual-barong-1.jpg']),
                'attributes' => json_encode([
                    'fabric' => 'Hugo Boss',
                    'color' => 'Light Blue',
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL']
                ]),
                'variants' => null,
                'weight' => 0.3,
                'dimensions' => '28x18x2',
                'care_instructions' => 'Machine wash cold. Hang dry.',
                'size_guide' => 'Please refer to our size chart for accurate measurements.',
                'is_active' => true,
                'sort_order' => 5,
                'category_id' => $casualBarongCategory->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}