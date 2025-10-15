<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;

echo "=== Manual Payment Status Update ===\n\n";

try {
    // Get all pending orders
    $pendingOrders = Order::where('payment_status', 'pending')->get();
    
    if ($pendingOrders->isEmpty()) {
        echo "❌ No pending orders found\n";
        exit(1);
    }
    
    echo "Found " . $pendingOrders->count() . " pending orders:\n";
    foreach ($pendingOrders as $index => $order) {
        echo ($index + 1) . ". {$order->order_number} - ₱{$order->total_amount} ({$order->payment_method})\n";
    }
    
    echo "\nEnter order number to update (or 'all' to update all): ";
    $input = trim(fgets(STDIN));
    
    if ($input === 'all') {
        $ordersToUpdate = $pendingOrders;
    } else {
        $order = Order::where('order_number', $input)->first();
        if (!$order) {
            echo "❌ Order not found\n";
            exit(1);
        }
        $ordersToUpdate = collect([$order]);
    }
    
    echo "\nSelect payment status:\n";
    echo "1. paid\n";
    echo "2. failed\n";
    echo "3. expired\n";
    echo "Enter choice (1-3): ";
    $statusChoice = trim(fgets(STDIN));
    
    $statusMap = [
        '1' => 'paid',
        '2' => 'failed', 
        '3' => 'expired'
    ];
    
    $newStatus = $statusMap[$statusChoice] ?? 'paid';
    
    foreach ($ordersToUpdate as $order) {
        echo "\n🔄 Updating order: {$order->order_number}\n";
        
        $order->payment_status = $newStatus;
        
        if ($newStatus === 'paid') {
            $order->status = 'processing';
            $order->transaction_id = 'MANUAL_' . time();
            $order->paid_at = now();
        } elseif ($newStatus === 'failed' || $newStatus === 'expired') {
            $order->status = 'cancelled';
        }
        
        $order->save();
        
        echo "✅ Updated to: {$order->payment_status}\n";
        echo "✅ Order status: {$order->status}\n";
        if ($order->transaction_id) {
            echo "✅ Transaction ID: {$order->transaction_id}\n";
        }
        if ($order->paid_at) {
            echo "✅ Paid at: {$order->paid_at}\n";
        }
    }
    
    echo "\n🎉 Payment status update completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
