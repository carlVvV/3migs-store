<?php

/**
 * Test Bux.ph Postback System
 * Run this to test the postback functionality
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\BuxPostbackService;
use App\Models\Order;

echo "=== Bux.ph Postback System Test ===\n\n";

try {
    $postbackService = new BuxPostbackService();
    
    // Find a test order
    $testOrder = Order::first();
    
    if (!$testOrder) {
        echo "❌ No orders found. Please create an order first.\n";
        exit(1);
    }
    
    echo "✅ Found test order: {$testOrder->order_number}\n";
    echo "✅ Current status: {$testOrder->status}\n";
    echo "✅ Current payment status: {$testOrder->payment_status}\n\n";
    
    // Test different payment statuses
    $testCases = [
        ['status' => 'paid', 'description' => 'Successful Payment'],
        ['status' => 'failed', 'description' => 'Failed Payment'],
        ['status' => 'pending', 'description' => 'Pending Payment'],
        ['status' => 'expired', 'description' => 'Expired Payment'],
    ];
    
    foreach ($testCases as $testCase) {
        echo "🧪 Testing: {$testCase['description']}\n";
        
        $result = $postbackService->testPostback($testOrder->order_number, $testCase['status']);
        
        if ($result['success']) {
            echo "✅ Success: {$result['message']}\n";
            
            // Refresh order to see changes
            $testOrder->refresh();
            echo "   - Order Status: {$testOrder->status}\n";
            echo "   - Payment Status: {$testOrder->payment_status}\n";
            echo "   - Transaction ID: {$testOrder->transaction_id}\n";
            echo "   - Paid At: {$testOrder->paid_at}\n";
        } else {
            echo "❌ Failed: {$result['message']}\n";
        }
        
        echo "\n";
        
        // Reset order for next test
        $testOrder->update([
            'status' => 'pending',
            'payment_status' => 'pending',
            'transaction_id' => null,
            'paid_at' => null
        ]);
    }
    
    echo "🎉 All postback tests completed!\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
