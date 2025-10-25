<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BarongProduct;

echo "Testing Best Selling Products Query\n";
echo "==================================\n\n";

// Test the best selling products query
$bestSellingProducts = BarongProduct::getBestSellingThisMonth(8);

echo "Best selling products count: " . $bestSellingProducts->count() . "\n\n";

if ($bestSellingProducts->count() > 0) {
    echo "Best selling products:\n";
    foreach ($bestSellingProducts as $index => $product) {
        echo ($index + 1) . ". {$product->name} - Monthly Sales: {$product->monthly_sales}\n";
    }
} else {
    echo "No best selling products found.\n";
}

echo "\nTesting individual query:\n";
$products = BarongProduct::where('is_available', true)
    ->orderBy('monthly_sales', 'desc')
    ->limit(8)
    ->get();

echo "Direct query count: " . $products->count() . "\n";
if ($products->count() > 0) {
    echo "Products with sales data:\n";
    foreach ($products as $index => $product) {
        echo ($index + 1) . ". {$product->name} - Monthly Sales: {$product->monthly_sales}\n";
    }
}

