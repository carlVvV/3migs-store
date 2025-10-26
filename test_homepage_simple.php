<?php

require_once 'vendor/autoload.php';

use App\Models\BarongProduct;
use App\Models\Category;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing HomeController queries...\n";

try {
    echo "1. Testing allProducts query...\n";
    $allProducts = BarongProduct::with(['category'])
        ->where('is_available', true)
        ->orderBy('created_at', 'desc')
        ->get();
    echo "   ✓ All products: " . $allProducts->count() . " items\n";

    echo "2. Testing featuredProducts query...\n";
    $featuredProducts = BarongProduct::with(['category'])
        ->featured()
        ->limit(8)
        ->get();
    echo "   ✓ Featured products: " . $featuredProducts->count() . " items\n";

    echo "3. Testing newArrivals query...\n";
    $newArrivals = BarongProduct::with(['category'])
        ->orderBy('created_at', 'desc')
        ->limit(8)
        ->get();
    echo "   ✓ New arrivals: " . $newArrivals->count() . " items\n";

    echo "4. Testing bestSellingProducts query...\n";
    $bestSellingProducts = BarongProduct::getBestSellingThisMonth(8);
    echo "   ✓ Best selling products: " . $bestSellingProducts->count() . " items\n";

    echo "5. Testing categories query...\n";
    $categories = Category::active()
        ->ordered()
        ->withCount('barongProducts')
        ->get();
    echo "   ✓ Categories: " . $categories->count() . " items\n";

    echo "\nAll queries completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

require_once 'vendor/autoload.php';

use App\Models\BarongProduct;
use App\Models\Category;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing HomeController queries...\n";

try {
    echo "1. Testing allProducts query...\n";
    $allProducts = BarongProduct::with(['category'])
        ->where('is_available', true)
        ->orderBy('created_at', 'desc')
        ->get();
    echo "   ✓ All products: " . $allProducts->count() . " items\n";

    echo "2. Testing featuredProducts query...\n";
    $featuredProducts = BarongProduct::with(['category'])
        ->featured()
        ->limit(8)
        ->get();
    echo "   ✓ Featured products: " . $featuredProducts->count() . " items\n";

    echo "3. Testing newArrivals query...\n";
    $newArrivals = BarongProduct::with(['category'])
        ->orderBy('created_at', 'desc')
        ->limit(8)
        ->get();
    echo "   ✓ New arrivals: " . $newArrivals->count() . " items\n";

    echo "4. Testing bestSellingProducts query...\n";
    $bestSellingProducts = BarongProduct::getBestSellingThisMonth(8);
    echo "   ✓ Best selling products: " . $bestSellingProducts->count() . " items\n";

    echo "5. Testing categories query...\n";
    $categories = Category::active()
        ->ordered()
        ->withCount('barongProducts')
        ->get();
    echo "   ✓ Categories: " . $categories->count() . " items\n";

    echo "\nAll queries completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}


