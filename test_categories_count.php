<?php

require_once 'vendor/autoload.php';

use App\Models\Category;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing categories withCount query...\n";

try {
    echo "1. Testing categories without withCount...\n";
    $categories = Category::active()
        ->ordered()
        ->get();
    echo "   ✓ Categories: " . $categories->count() . " items\n";

    echo "2. Testing categories with withCount...\n";
    $categoriesWithCount = Category::active()
        ->ordered()
        ->withCount('barongProducts')
        ->get();
    echo "   ✓ Categories with count: " . $categoriesWithCount->count() . " items\n";
    
    foreach ($categoriesWithCount as $category) {
        echo "   - {$category->name}: {$category->barong_products_count} products\n";
    }

    echo "\nCategories withCount query completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

require_once 'vendor/autoload.php';

use App\Models\Category;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing categories withCount query...\n";

try {
    echo "1. Testing categories without withCount...\n";
    $categories = Category::active()
        ->ordered()
        ->get();
    echo "   ✓ Categories: " . $categories->count() . " items\n";

    echo "2. Testing categories with withCount...\n";
    $categoriesWithCount = Category::active()
        ->ordered()
        ->withCount('barongProducts')
        ->get();
    echo "   ✓ Categories with count: " . $categoriesWithCount->count() . " items\n";
    
    foreach ($categoriesWithCount as $category) {
        echo "   - {$category->name}: {$category->barong_products_count} products\n";
    }

    echo "\nCategories withCount query completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}


