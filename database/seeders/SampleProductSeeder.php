<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class SampleProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create categories
        $barongCategory = Category::firstOrCreate(
            ['slug' => 'barong'],
            [
                'name' => 'Barong',
                'description' => 'Traditional Filipino formal wear',
                'is_active' => true
            ]
        );

        $gownCategory = Category::firstOrCreate(
            ['slug' => 'gowns'],
            [
                'name' => 'Gowns',
                'description' => 'Elegant formal gowns',
                'is_active' => true
            ]
        );

        $accessoriesCategory = Category::firstOrCreate(
            ['slug' => 'accessories'],
            [
                'name' => 'Accessories',
                'description' => 'Fashion accessories',
                'is_active' => true
            ]
        );

        // Sample Products
        $products = [
            [
                'name' => 'Classic White Barong Tagalog',
                'description' => 'Traditional Filipino formal shirt made from premium jusi fabric. Perfect for weddings, formal events, and special occasions.',
                'price' => 3000.00,
                'sale_price' => 2500.00,
                'category_id' => $barongCategory->id,
                'stock_quantity' => 15,
                'is_active' => true,
                'in_stock' => true,
                'manage_stock' => true,
                'images' => json_encode([
                    '/images/barong-white-classic.jpg',
                    '/images/barong-white-classic-2.jpg'
                ]),
                'attributes' => json_encode([
                    'Fabric' => 'Premium Jusi',
                    'Color' => 'White',
                    'Style' => 'Classic',
                    'Care' => 'Dry Clean Only'
                ])
            ],
            [
                'name' => 'Embroidered Barong with Gold Thread',
                'description' => 'Elegant barong with intricate gold thread embroidery. A perfect choice for special celebrations and formal gatherings.',
                'price' => 4000.00,
                'sale_price' => 3500.00,
                'category_id' => $barongCategory->id,
                'stock_quantity' => 8,
                'is_active' => true,
                'in_stock' => true,
                'manage_stock' => true,
                'images' => json_encode([
                    '/images/barong-gold-embroidered.jpg'
                ]),
                'attributes' => json_encode([
                    'Fabric' => 'Premium Jusi',
                    'Color' => 'White',
                    'Style' => 'Embroidered',
                    'Care' => 'Dry Clean Only'
                ])
            ],
            [
                'name' => 'Modern Slim Fit Barong',
                'description' => 'Contemporary take on the traditional barong with a modern slim fit. Perfect for young professionals and modern events.',
                'price' => 2200.00,
                'sale_price' => 1800.00,
                'category_id' => $barongCategory->id,
                'stock_quantity' => 20,
                'is_active' => true,
                'in_stock' => true,
                'manage_stock' => true,
                'images' => json_encode([
                    '/images/barong-modern-slim.jpg'
                ]),
                'attributes' => json_encode([
                    'Fabric' => 'Cotton Blend',
                    'Color' => 'White',
                    'Style' => 'Modern Slim Fit',
                    'Care' => 'Machine Washable'
                ])
            ],
            [
                'name' => 'Wedding Barong Set',
                'description' => 'Complete wedding barong set with matching pants. Perfect for grooms and wedding parties.',
                'price' => 5500.00,
                'sale_price' => 4500.00,
                'category_id' => $barongCategory->id,
                'stock_quantity' => 5,
                'is_active' => true,
                'in_stock' => true,
                'manage_stock' => true,
                'images' => json_encode([
                    '/images/barong-wedding-set.jpg'
                ]),
                'attributes' => json_encode([
                    'Fabric' => 'Premium Jusi',
                    'Color' => 'White',
                    'Style' => 'Wedding Set',
                    'Care' => 'Dry Clean Only'
                ])
            ],
            [
                'name' => 'Elegant Evening Gown',
                'description' => 'Stunning evening gown perfect for formal events, galas, and special occasions. Features elegant draping and sophisticated design.',
                'price' => 3800.00,
                'sale_price' => 3200.00,
                'category_id' => $gownCategory->id,
                'stock_quantity' => 12,
                'is_active' => true,
                'in_stock' => true,
                'manage_stock' => true,
                'images' => json_encode([
                    '/images/gown-elegant-evening.jpg'
                ]),
                'attributes' => json_encode([
                    'Fabric' => 'Chiffon',
                    'Color' => 'Black',
                    'Style' => 'Evening Gown',
                    'Care' => 'Dry Clean Only'
                ])
            ],
            [
                'name' => 'Cocktail Party Dress',
                'description' => 'Chic cocktail dress perfect for parties and semi-formal events. Features modern design and comfortable fit.',
                'price' => 1800.00,
                'sale_price' => 1500.00,
                'category_id' => $gownCategory->id,
                'stock_quantity' => 18,
                'is_active' => true,
                'in_stock' => true,
                'manage_stock' => true,
                'images' => json_encode([
                    '/images/gown-cocktail-party.jpg'
                ]),
                'attributes' => json_encode([
                    'Fabric' => 'Polyester',
                    'Color' => 'Red',
                    'Style' => 'Cocktail',
                    'Care' => 'Machine Washable'
                ])
            ],
            [
                'name' => 'Barong Accessories Set',
                'description' => 'Complete accessories set for barong including cufflinks, tie, and pocket square. Perfect complement to any barong outfit.',
                'price' => 1000.00,
                'sale_price' => 800.00,
                'category_id' => $accessoriesCategory->id,
                'stock_quantity' => 25,
                'is_active' => true,
                'in_stock' => true,
                'manage_stock' => true,
                'images' => json_encode([
                    '/images/accessories-barong-set.jpg'
                ]),
                'attributes' => json_encode([
                    'Type' => 'Accessories Set',
                    'Color' => 'Black',
                    'Style' => 'Formal',
                    'Care' => 'Hand Wash'
                ])
            ],
            [
                'name' => 'Formal Shoes - Black Leather',
                'description' => 'Classic black leather formal shoes perfect for barong and formal wear. Comfortable and stylish.',
                'price' => 1500.00,
                'sale_price' => 1200.00,
                'category_id' => $accessoriesCategory->id,
                'stock_quantity' => 30,
                'is_active' => true,
                'in_stock' => true,
                'manage_stock' => true,
                'images' => json_encode([
                    '/images/shoes-black-leather.jpg'
                ]),
                'attributes' => json_encode([
                    'Material' => 'Genuine Leather',
                    'Color' => 'Black',
                    'Style' => 'Formal',
                    'Care' => 'Leather Care'
                ])
            ]
        ];

        // Create all products
        foreach ($products as $productData) {
            $productData['slug'] = Str::slug($productData['name']);
            $productData['sku'] = '3MIGS-' . strtoupper(Str::random(8));
            $productData['weight'] = rand(200, 800); // grams
            $productData['dimensions'] = json_encode([
                'length' => rand(60, 80),
                'width' => rand(40, 60),
                'height' => rand(2, 5)
            ]);
            $productData['created_at'] = now();
            $productData['updated_at'] = now();

            Product::create($productData);
        }

        $this->command->info('Sample products created successfully!');
        $this->command->info('Created ' . count($products) . ' sample products');
    }
}