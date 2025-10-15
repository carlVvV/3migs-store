<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BuxPostbackService
{
    private ?string $webhookSecret;

    public function __construct()
    {
        $this->webhookSecret = config('services.bux.webhook_secret');
    }

    /**
     * Process incoming postback notification from Bux.ph
     */
    public function processPostback(array $payload): array
    {
        try {
            // Normalize id/key for idempotency
            $ref = $payload['refno'] ?? $payload['req_id'] ?? null;

            // Validate the postback signature (will be upgraded to raw-body validation in controller)
            if (!$this->validateSignature($payload)) {
                Log::warning('Bux.ph postback signature validation failed', [
                    'payload' => $payload
                ]);
                return ['success' => false, 'message' => 'Invalid signature'];
            }

            // Extract order information
            $orderNumber = $payload['req_id'] ?? $payload['refno'] ?? null;
            $status = $payload['status'] ?? null;
            $transactionId = $payload['transaction_id'] ?? ($payload['refno'] ?? null);
            $amount = $payload['amount'] ?? null;
            $paymentMethod = $payload['payment_method'] ?? ($payload['method'] ?? null);

            if (!$orderNumber) {
                Log::error('Bux.ph postback missing order number', ['payload' => $payload]);
                return ['success' => false, 'message' => 'Missing order number'];
            }

            // Idempotency: ignore duplicate postbacks (based on ref/req_id)
            if ($ref) {
                $dupe = DB::table('processed_webhooks')->where('provider', 'bux')->where('idempotency_key', $ref)->exists();
                if ($dupe) {
                    return ['success' => true, 'message' => 'Duplicate postback ignored'];
                }
            }

            // Find the order
            $order = Order::where('order_number', $orderNumber)->first();
            if (!$order) {
                Log::error('Bux.ph postback order not found', [
                    'order_number' => $orderNumber,
                    'payload' => $payload
                ]);
                return ['success' => false, 'message' => 'Order not found'];
            }

            // Process based on status
            $result = $this->processOrderStatus($order, $status, [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payload' => $payload
            ]);

            // Record idempotency on success
            if (!empty($result['success']) && $ref) {
                DB::table('processed_webhooks')->insert([
                    'provider' => 'bux',
                    'idempotency_key' => $ref,
                    'processed_at' => now(),
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Bux.ph postback processing error', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            return ['success' => false, 'message' => 'Processing error'];
        }
    }

    /**
     * Validate the postback signature
     */
    private function validateSignature(array $payload): bool
    {
        if (empty($this->webhookSecret)) {
            Log::warning('Bux.ph webhook secret not configured');
            return true; // Allow if no secret configured (development)
        }

        $signature = $payload['signature'] ?? '';
        $expectedSignature = $this->generateSignature($payload);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate expected signature for validation
     */
    private function generateSignature(array $payload): string
    {
        // Remove signature from payload for calculation
        $dataToSign = $payload;
        unset($dataToSign['signature']);

        // Sort by keys and create query string
        ksort($dataToSign);
        $queryString = http_build_query($dataToSign);

        // Generate HMAC signature
        return hash_hmac('sha256', $queryString, $this->webhookSecret);
    }

    /**
     * Process order status based on payment notification
     */
    private function processOrderStatus(Order $order, string $status, array $paymentData): array
    {
        DB::beginTransaction();

        try {
            switch ($status) {
                case 'paid':
                case 'completed':
                    return $this->handlePaidOrder($order, $paymentData);

                case 'failed':
                case 'cancelled':
                    return $this->handleFailedOrder($order, $paymentData);

                case 'pending':
                    return $this->handlePendingOrder($order, $paymentData);

                case 'expired':
                    return $this->handleExpiredOrder($order, $paymentData);

                default:
                    Log::warning('Unknown Bux.ph postback status', [
                        'order_id' => $order->id,
                        'status' => $status,
                        'payment_data' => $paymentData
                    ]);
                    return ['success' => false, 'message' => 'Unknown status'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing order status', [
                'order_id' => $order->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    /**
     * Handle successful payment
     */
    private function handlePaidOrder(Order $order, array $paymentData): array
    {
        // Update order with payment information
        $order->payment_status = 'paid';
        
        // Update payment method based on Bux.ph response
        $buxPaymentMethod = $paymentData['payment_method'] ?? 'gcash';
        $order->payment_method = $this->mapBuxPaymentMethod($buxPaymentMethod);
        
        $order->transaction_id = $paymentData['transaction_id'] ?? null;
        $order->paid_at = now();

        // Update order status if still pending
        if ($order->status === 'pending') {
            $order->status = 'processing';
        }

        $order->save();

        // Log successful payment
        Log::info('Order payment successful', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'original_payment_method' => $order->getOriginal('payment_method'),
            'new_payment_method' => $order->payment_method,
            'amount' => $paymentData['amount'],
            'transaction_id' => $paymentData['transaction_id'],
            'bux_payment_method' => $buxPaymentMethod
        ]);

        // TODO: Send confirmation email to customer
        // TODO: Notify admin of new paid order
        // TODO: Update inventory if needed

        DB::commit();

        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status,
            'order_status' => $order->status,
            'transaction_id' => $order->transaction_id,
            'payment_method' => $order->payment_method
        ];
    }

    /**
     * Handle failed payment
     */
    private function handleFailedOrder(Order $order, array $paymentData): array
    {
        $order->payment_status = 'failed';
        $order->status = 'cancelled';
        $order->save();

        Log::info('Order payment failed', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_data' => $paymentData
        ]);

        // TODO: Send failure notification to customer
        // TODO: Restore inventory if needed

        DB::commit();

        return [
            'success' => true,
            'message' => 'Payment failure processed',
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ];
    }

    /**
     * Handle pending payment
     */
    private function handlePendingOrder(Order $order, array $paymentData): array
    {
        $order->payment_status = 'pending';
        $order->save();

        Log::info('Order payment pending', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_data' => $paymentData
        ]);

        DB::commit();

        return [
            'success' => true,
            'message' => 'Payment pending processed',
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ];
    }

    /**
     * Handle expired payment
     */
    private function handleExpiredOrder(Order $order, array $paymentData): array
    {
        $order->payment_status = 'expired';
        $order->status = 'cancelled';
        $order->save();

        Log::info('Order payment expired', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_data' => $paymentData
        ]);

        // TODO: Send expiration notification to customer
        // TODO: Restore inventory if needed

        DB::commit();

        return [
            'success' => true,
            'message' => 'Payment expiration processed',
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ];
    }

    /**
     * Map Bux.ph payment method to our internal payment method
     */
    private function mapBuxPaymentMethod(string $buxMethod): string
    {
        $mapping = [
            'gcash' => 'gcash',
            'gcash_qr' => 'gcash',
            'grabpay' => 'grabpay',
            'paymaya' => 'paymaya',
            'bdo' => 'bank_transfer',
            'bpi' => 'bank_transfer',
            'metrobank' => 'bank_transfer',
            'unionbank' => 'bank_transfer',
            'credit_card' => 'credit_card',
            'debit_card' => 'credit_card',
        ];

        return $mapping[strtolower($buxMethod)] ?? 'gcash';
    }

    /**
     * Test postback processing (for development)
     */
    public function testPostback(string $orderNumber, string $status = 'paid'): array
    {
        $testPayload = [
            'req_id' => $orderNumber,
            'status' => $status,
            'transaction_id' => 'TEST_' . time(),
            'amount' => 1000.00,
            'payment_method' => 'gcash',
            'timestamp' => now()->toISOString(),
            'signature' => 'test_signature' // Add test signature
        ];

        // Temporarily disable signature validation for testing
        $originalSecret = $this->webhookSecret;
        $this->webhookSecret = null; // This will bypass validation
        
        $result = $this->processPostback($testPayload);
        
        // Restore original secret
        $this->webhookSecret = $originalSecret;
        
        return $result;
    }
}
