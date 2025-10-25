<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;

echo "Testing HomeController\n";
echo "=====================\n\n";

$controller = new HomeController();
$request = new Request();

try {
    $response = $controller->index();
    $data = $response->getData();
    
    echo "Response type: " . get_class($response) . "\n";
    
    if (method_exists($response, 'getData')) {
        $viewData = $response->getData();
        echo "View data keys: " . implode(', ', array_keys($viewData)) . "\n";
        
        if (isset($viewData['bestSellingProducts'])) {
            echo "Best selling products count: " . $viewData['bestSellingProducts']->count() . "\n";
        } else {
            echo "bestSellingProducts not found in view data\n";
        }
        
        if (isset($viewData['allProducts'])) {
            echo "All products count: " . $viewData['allProducts']->count() . "\n";
        } else {
            echo "allProducts not found in view data\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

