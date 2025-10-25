<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarongProduct;
use App\Models\Category;
use App\Models\Brand;

class TestProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test products for homepage rotation...');

        // Get or create categories
        $categories = [
            'Traditional Barong' => Category::firstOrCreate(['name' => 'Traditional Barong'], [
                'slug' => 'traditional-barong',
                'description' => 'Classic Filipino barong designs',
                'is_active' => true,
                'sort_order' => 1
            ]),
            'Modern Barong' => Category::firstOrCreate(['name' => 'Modern Barong'], [
                'slug' => 'modern-barong',
                'description' => 'Contemporary barong styles',
                'is_active' => true,
                'sort_order' => 2
            ]),
            'Wedding Barong' => Category::firstOrCreate(['name' => 'Wedding Barong'], [
                'slug' => 'wedding-barong',
                'description' => 'Elegant barong for special occasions',
                'is_active' => true,
                'sort_order' => 3
            ]),
            'Formal Barong' => Category::firstOrCreate(['name' => 'Formal Barong'], [
                'slug' => 'formal-barong',
                'description' => 'Professional and formal barong',
                'is_active' => true,
                'sort_order' => 4
            ])
        ];

        // Get or create brands
        $brands = [
            '3Migs Premium' => Brand::firstOrCreate(['name' => '3Migs Premium'], [
                'slug' => '3migs-premium',
                'description' => 'Premium quality barong collection',
                'is_active' => true
            ]),
            'Heritage Collection' => Brand::firstOrCreate(['name' => 'Heritage Collection'], [
                'slug' => 'heritage-collection',
                'description' => 'Traditional Filipino heritage designs',
                'is_active' => true
            ]),
            'Modern Elegance' => Brand::firstOrCreate(['name' => 'Modern Elegance'], [
                'slug' => 'modern-elegance',
                'description' => 'Contemporary barong designs',
                'is_active' => true
            ])
        ];

        // Sample products data
        $products = [
            // Featured Products (8 products)
            [
                'name' => 'Premium Jusi Barong Tagalog',
                'sku' => 'FEA-001',
                'slug' => 'premium-jusi-barong-tagalog',
                'description' => 'Handcrafted jusi barong with intricate embroidery and premium fabric.',
                'type' => 'Traditional Barong',
                'base_price' => 2500.00,
                'special_price' => 2000.00,
                'category_id' => $categories['Traditional Barong']->id,
                'brand_id' => $brands['3Migs Premium']->id,
                'stock' => 15,
                'is_featured' => true,
                'is_available' => true,
                'average_rating' => 4.8,
                'review_count' => 24,
                'monthly_sales' => 12,
                'sales_count' => 45
            ],
            [
                'name' => 'Elegant Piña Barong',
                'sku' => 'FEA-002',
                'slug' => 'elegant-pina-barong',
                'description' => 'Luxurious piña fabric barong perfect for formal occasions.',
                'base_price' => 3500.00,
                'special_price' => null,
                'category_id' => $categories['Wedding Barong']->id,
                'brand_id' => $brands['Heritage Collection']->id,
                'stock' => 8,
                'is_featured' => true,
                'is_available' => true,
                'average_rating' => 4.9,
                'review_count' => 18,
                'monthly_sales' => 8,
                'sales_count' => 32
            ],
            [
                'name' => 'Modern Slim Fit Barong',
                'sku' => 'FEA-003',
                'slug' => 'modern-slim-fit-barong',
                'description' => 'Contemporary slim-fit barong with modern styling.',
                'base_price' => 2200.00,
                'special_price' => 1800.00,
                'category_id' => $categories['Modern Barong']->id,
                'brand_id' => $brands['Modern Elegance']->id,
                'stock' => 20,
                'is_featured' => true,
                'is_available' => true,
                'average_rating' => 4.7,
                'review_count' => 31,
                'monthly_sales' => 15,
                'sales_count' => 58
            ],
            [
                'name' => 'Classic White Barong',
                'sku' => 'FEA-004',
                'slug' => 'classic-white-barong',
                'description' => 'Timeless white barong for traditional occasions.',
                'base_price' => 1800.00,
                'special_price' => null,
                'category_id' => $categories['Traditional Barong']->id,
                'brand_id' => $brands['Heritage Collection']->id,
                'stock' => 25,
                'is_featured' => true,
                'is_available' => true,
                'average_rating' => 4.6,
                'review_count' => 42,
                'monthly_sales' => 20,
                'sales_count' => 78
            ],
            [
                'name' => 'Embroidered Formal Barong',
                'sku' => 'FEA-005',
                'slug' => 'embroidered-formal-barong',
                'description' => 'Elegant embroidered barong for business and formal events.',
                'base_price' => 2800.00,
                'special_price' => 2300.00,
                'category_id' => $categories['Formal Barong']->id,
                'brand_id' => $brands['3Migs Premium']->id,
                'stock' => 12,
                'is_featured' => true,
                'is_available' => true,
                'average_rating' => 4.8,
                'review_count' => 19,
                'monthly_sales' => 10,
                'sales_count' => 35
            ],
            [
                'name' => 'Wedding Ceremony Barong',
                'sku' => 'FEA-006',
                'slug' => 'wedding-ceremony-barong',
                'description' => 'Special barong designed for wedding ceremonies.',
                'base_price' => 4000.00,
                'special_price' => 3200.00,
                'category_id' => $categories['Wedding Barong']->id,
                'brand_id' => $brands['Heritage Collection']->id,
                'stock' => 6,
                'is_featured' => true,
                'is_available' => true,
                'average_rating' => 4.9,
                'review_count' => 12,
                'monthly_sales' => 6,
                'sales_count' => 22
            ],
            [
                'name' => 'Contemporary Barong Shirt',
                'sku' => 'FEA-007',
                'slug' => 'contemporary-barong-shirt',
                'description' => 'Modern barong shirt with contemporary design elements.',
                'base_price' => 2000.00,
                'special_price' => null,
                'category_id' => $categories['Modern Barong']->id,
                'brand_id' => $brands['Modern Elegance']->id,
                'stock' => 18,
                'is_featured' => true,
                'is_available' => true,
                'average_rating' => 4.5,
                'review_count' => 28,
                'monthly_sales' => 14,
                'sales_count' => 52
            ],
            [
                'name' => 'Premium Silk Barong',
                'sku' => 'FEA-008',
                'slug' => 'premium-silk-barong',
                'description' => 'Luxurious silk barong with premium craftsmanship.',
                'base_price' => 3200.00,
                'special_price' => 2600.00,
                'category_id' => $categories['Traditional Barong']->id,
                'brand_id' => $brands['3Migs Premium']->id,
                'stock' => 10,
                'is_featured' => true,
                'is_available' => true,
                'average_rating' => 4.9,
                'review_count' => 15,
                'monthly_sales' => 7,
                'sales_count' => 28
            ],

            // New Arrivals (8 products)
            [
                'name' => 'New Collection Barong 2024',
                'sku' => 'NEW-001',
                'slug' => 'new-collection-barong-2024',
                'description' => 'Latest 2024 collection featuring modern Filipino design.',
                'base_price' => 2400.00,
                'special_price' => null,
                'category_id' => $categories['Modern Barong']->id,
                'brand_id' => $brands['Modern Elegance']->id,
                'stock' => 30,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.6,
                'review_count' => 8,
                'monthly_sales' => 25,
                'sales_count' => 25
            ],
            [
                'name' => 'Fresh Design Barong',
                'sku' => 'NEW-002',
                'slug' => 'fresh-design-barong',
                'description' => 'Fresh new design with contemporary styling.',
                'base_price' => 2100.00,
                'special_price' => 1700.00,
                'category_id' => $categories['Modern Barong']->id,
                'brand_id' => $brands['3Migs Premium']->id,
                'stock' => 22,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.7,
                'review_count' => 12,
                'monthly_sales' => 18,
                'sales_count' => 18
            ],
            [
                'name' => 'Latest Traditional Barong',
                'sku' => 'NEW-003',
                'slug' => 'latest-traditional-barong',
                'description' => 'New traditional barong with updated craftsmanship.',
                'base_price' => 2600.00,
                'special_price' => null,
                'category_id' => $categories['Traditional Barong']->id,
                'brand_id' => $brands['Heritage Collection']->id,
                'stock' => 16,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.8,
                'review_count' => 6,
                'monthly_sales' => 12,
                'sales_count' => 12
            ],
            [
                'name' => 'Modern Wedding Barong',
                'sku' => 'NEW-004',
                'slug' => 'modern-wedding-barong',
                'description' => 'Contemporary wedding barong for modern couples.',
                'base_price' => 3800.00,
                'special_price' => 3000.00,
                'category_id' => $categories['Wedding Barong']->id,
                'brand_id' => $brands['Modern Elegance']->id,
                'stock' => 8,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.9,
                'review_count' => 4,
                'monthly_sales' => 6,
                'sales_count' => 6
            ],
            [
                'name' => 'Executive Barong Collection',
                'sku' => 'NEW-005',
                'slug' => 'executive-barong-collection',
                'description' => 'Professional executive barong for business occasions.',
                'base_price' => 2900.00,
                'special_price' => null,
                'category_id' => $categories['Formal Barong']->id,
                'brand_id' => $brands['3Migs Premium']->id,
                'stock' => 14,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.7,
                'review_count' => 9,
                'monthly_sales' => 10,
                'sales_count' => 10
            ],
            [
                'name' => 'Contemporary Formal Barong',
                'sku' => 'NEW-006',
                'slug' => 'contemporary-formal-barong',
                'description' => 'Modern formal barong with contemporary design.',
                'base_price' => 2300.00,
                'special_price' => 1900.00,
                'category_id' => $categories['Formal Barong']->id,
                'brand_id' => $brands['Modern Elegance']->id,
                'stock' => 20,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.6,
                'review_count' => 7,
                'monthly_sales' => 16,
                'sales_count' => 16
            ],
            [
                'name' => 'Heritage Revival Barong',
                'sku' => 'NEW-007',
                'slug' => 'heritage-revival-barong',
                'description' => 'Revival of classic heritage designs with modern touch.',
                'base_price' => 2700.00,
                'special_price' => null,
                'category_id' => $categories['Traditional Barong']->id,
                'brand_id' => $brands['Heritage Collection']->id,
                'stock' => 12,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.8,
                'review_count' => 5,
                'monthly_sales' => 8,
                'sales_count' => 8
            ],
            [
                'name' => 'Urban Barong Style',
                'sku' => 'NEW-008',
                'slug' => 'urban-barong-style',
                'description' => 'Urban-inspired barong for city professionals.',
                'base_price' => 2200.00,
                'special_price' => 1800.00,
                'category_id' => $categories['Modern Barong']->id,
                'brand_id' => $brands['Modern Elegance']->id,
                'stock' => 25,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.5,
                'review_count' => 11,
                'monthly_sales' => 20,
                'sales_count' => 20
            ],

            // Best Selling Products (8 products)
            [
                'name' => 'Best Seller Classic Barong',
                'sku' => 'BEST-001',
                'slug' => 'best-seller-classic-barong',
                'description' => 'Our most popular classic barong design.',
                'base_price' => 2000.00,
                'special_price' => null,
                'category_id' => $categories['Traditional Barong']->id,
                'brand_id' => $brands['Heritage Collection']->id,
                'stock' => 35,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.8,
                'review_count' => 156,
                'monthly_sales' => 45,
                'sales_count' => 234
            ],
            [
                'name' => 'Top Rated Wedding Barong',
                'sku' => 'BEST-002',
                'slug' => 'top-rated-wedding-barong',
                'description' => 'Highly rated wedding barong for special occasions.',
                'base_price' => 3500.00,
                'special_price' => 2800.00,
                'category_id' => $categories['Wedding Barong']->id,
                'brand_id' => $brands['3Migs Premium']->id,
                'stock' => 18,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.9,
                'review_count' => 89,
                'monthly_sales' => 32,
                'sales_count' => 187
            ],
            [
                'name' => 'Popular Modern Barong',
                'sku' => 'BEST-003',
                'slug' => 'popular-modern-barong',
                'description' => 'Popular modern barong with contemporary styling.',
                'base_price' => 2300.00,
                'special_price' => null,
                'category_id' => $categories['Modern Barong']->id,
                'brand_id' => $brands['Modern Elegance']->id,
                'stock' => 28,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.7,
                'review_count' => 134,
                'monthly_sales' => 38,
                'sales_count' => 198
            ],
            [
                'name' => 'Executive Choice Barong',
                'sku' => 'BEST-004',
                'slug' => 'executive-choice-barong',
                'description' => 'Executive choice barong for business professionals.',
                'base_price' => 2800.00,
                'special_price' => 2200.00,
                'category_id' => $categories['Formal Barong']->id,
                'brand_id' => $brands['3Migs Premium']->id,
                'stock' => 22,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.8,
                'review_count' => 98,
                'monthly_sales' => 28,
                'sales_count' => 156
            ],
            [
                'name' => 'Customer Favorite Barong',
                'sku' => 'BEST-005',
                'slug' => 'customer-favorite-barong',
                'description' => 'Customer favorite barong with excellent reviews.',
                'base_price' => 2100.00,
                'special_price' => null,
                'category_id' => $categories['Traditional Barong']->id,
                'brand_id' => $brands['Heritage Collection']->id,
                'stock' => 32,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.9,
                'review_count' => 167,
                'monthly_sales' => 42,
                'sales_count' => 245
            ],
            [
                'name' => 'Premium Best Seller',
                'sku' => 'BEST-006',
                'slug' => 'premium-best-seller',
                'description' => 'Premium best seller barong with luxury materials.',
                'base_price' => 3200.00,
                'special_price' => 2600.00,
                'category_id' => $categories['Wedding Barong']->id,
                'brand_id' => $brands['3Migs Premium']->id,
                'stock' => 15,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.9,
                'review_count' => 76,
                'monthly_sales' => 25,
                'sales_count' => 134
            ],
            [
                'name' => 'Trending Modern Barong',
                'sku' => 'BEST-007',
                'slug' => 'trending-modern-barong',
                'description' => 'Trending modern barong with contemporary appeal.',
                'base_price' => 2400.00,
                'special_price' => 1900.00,
                'category_id' => $categories['Modern Barong']->id,
                'brand_id' => $brands['Modern Elegance']->id,
                'stock' => 26,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.7,
                'review_count' => 112,
                'monthly_sales' => 35,
                'sales_count' => 178
            ],
            [
                'name' => 'Classic Best Seller',
                'sku' => 'BEST-008',
                'slug' => 'classic-best-seller',
                'description' => 'Classic best seller barong with timeless design.',
                'base_price' => 1900.00,
                'special_price' => null,
                'category_id' => $categories['Traditional Barong']->id,
                'brand_id' => $brands['Heritage Collection']->id,
                'stock' => 40,
                'is_featured' => false,
                'is_available' => true,
                'average_rating' => 4.8,
                'review_count' => 189,
                'monthly_sales' => 48,
                'sales_count' => 267
            ]
        ];

        // Create products
        foreach ($products as $productData) {
            // Add type field based on category name
            $categoryId = $productData['category_id'];
            $type = 'Traditional Barong'; // default
            
            if ($categoryId == $categories['Traditional Barong']->id) {
                $type = 'Traditional Barong';
            } elseif ($categoryId == $categories['Modern Barong']->id) {
                $type = 'Modern Barong';
            } elseif ($categoryId == $categories['Wedding Barong']->id) {
                $type = 'Wedding Barong';
            } elseif ($categoryId == $categories['Formal Barong']->id) {
                $type = 'Formal Barong';
            }
            
            $productData['type'] = $type;
            BarongProduct::create($productData);
        }

        $this->command->info('✓ Created ' . count($products) . ' test products');
        $this->command->info('✓ Featured Products: 8');
        $this->command->info('✓ New Arrivals: 8');
        $this->command->info('✓ Best Selling Products: 8');
        $this->command->info('✓ Total Products: 24');
        $this->command->info('✓ All products have proper sales data for rotation testing');
    }
}
