<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking products and their availability...\n";

$products = \App\Models\BarongProduct::all(['id', 'name', 'slug', 'is_available', 'size_stocks']);

foreach ($products as $product) {
    echo "ID: {$product->id}, Name: {$product->name}, Available: " . ($product->is_available ? 'Yes' : 'No') . "\n";
    echo "Slug: {$product->slug}\n";
    echo "Size Stocks: " . json_encode($product->size_stocks) . "\n";
    echo "---\n";
}

echo "\nChecking categories...\n";
$categories = \App\Models\Category::all(['id', 'name', 'slug']);
foreach ($categories as $category) {
    echo "ID: {$category->id}, Name: {$category->name}, Slug: {$category->slug}\n";
}






