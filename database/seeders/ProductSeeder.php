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

        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}