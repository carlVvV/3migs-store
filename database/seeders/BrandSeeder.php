<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => '3Migs',
                'slug' => '3migs',
                'description' => 'Premium Filipino Barong and Traditional Wear',
                'is_active' => true,
            ],
            [
                'name' => 'Barong Manila',
                'slug' => 'barong-manila',
                'description' => 'Traditional Barong Tagalog from Manila',
                'is_active' => true,
            ],
            [
                'name' => 'Filipiniana Couture',
                'slug' => 'filipiniana-couture',
                'description' => 'Modern Filipiniana Dresses and Formal Wear',
                'is_active' => true,
            ],
            [
                'name' => 'Heritage Barong',
                'slug' => 'heritage-barong',
                'description' => 'Authentic Traditional Barong Designs',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
