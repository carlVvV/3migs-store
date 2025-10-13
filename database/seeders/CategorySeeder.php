<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Traditional Barong',
                'slug' => 'traditional-barong',
                'description' => 'Classic traditional Filipino barong with intricate embroidery',
                'image' => 'images/categories/traditional-barong.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Modern Barong',
                'slug' => 'modern-barong',
                'description' => 'Contemporary barong designs with modern cuts and styles',
                'image' => 'images/categories/modern-barong.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Wedding Barong',
                'slug' => 'wedding-barong',
                'description' => 'Elegant barong for special wedding occasions',
                'image' => 'images/categories/wedding-barong.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Formal Barong',
                'slug' => 'formal-barong',
                'description' => 'Professional barong for formal events and business occasions',
                'image' => 'images/categories/formal-barong.jpg',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Casual Barong',
                'slug' => 'casual-barong',
                'description' => 'Comfortable barong for everyday casual wear',
                'image' => 'images/categories/casual-barong.jpg',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}