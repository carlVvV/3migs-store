<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BuxService
{
    private string $baseUrl;
    private string $checkoutUrl;
    private ?string $apiKey;
    private ?string $secret;
    private ?string $merchantId;

    public function __construct()
    {
        $this->baseUrl = config('services.bux.base_url'); // For redirecting users
        $this->checkoutUrl = config('services.bux.checkout_url'); // For generating checkout
        $this->apiKey = config('services.bux.api_key');
        $this->secret = config('services.bux.secret');
        $this->merchantId = config('services.bux.merchant_id');
    }

    public function generateCheckoutUrl(array $payload): array
    {
        // Use the correct checkout URL for generating checkout
        $url = $this->checkoutUrl;

        // Prepare the payload according to Bux.ph API format
        $apiPayload = [
            'client_id' => $this->merchantId,
            'req_id' => $payload['req_id'],
            'amount' => $payload['amount'],
            'description' => $payload['description'],
            'email' => $payload['email'],
            'expiry' => $payload['expiry'] ?? 2,
            'notification_url' => $payload['notification_url'],
            'redirect_url' => $payload['redirect_url'],
            'name' => $payload['name'],
            'contact' => $payload['contact'],
            'param1' => $payload['param1'] ?? $payload['req_id'],
        ];

        // Use x-api-key authentication as specified
        $response = $this->requestWithAuthStyle($url, $apiPayload, 'x-api-key');
        
        if ($response->ok()) {
            return [
                'success' => true,
                'data' => $response->json(),
            ];
        }

        \Log::error('Bux checkout creation failed', [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
            'payload' => $apiPayload,
            'headers' => $response->headers(),
        ]);

        // Handle specific error cases
        $errorMessage = 'Payment service temporarily unavailable';
        if ($response->status() == 403) {
            $errorMessage = 'Payment service authentication failed. Please contact support.';
        } elseif ($response->status() == 400) {
            $errorMessage = 'Invalid payment request. Please check your information.';
        } elseif ($response->status() == 500) {
            $errorMessage = 'Payment service error. Please try again later.';
        }

        return [
            'success' => false,
            'status' => $response->status(),
            'error' => $errorMessage,
            'raw_error' => $response->body(),
        ];
    }
    private function requestWithAuthStyle(string $url, array $payload, string $style)
    {
        $client = Http::acceptJson()->asJson();
        $client = $client->withHeaders(["x-api-key" => $this->apiKey ?? '']);

        return $client->post($url, $payload);
    }

    /**
     * Get the base URL for redirecting users to checkout
     */
    public function getRedirectUrl(): string
    {
        return $this->baseUrl;
    }
}


