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

        // Add client_id (merchantId) to the payload
        $payloadWithClientId = array_merge($payload, [
            'client_id' => $this->merchantId,
        ]);

        // Use x-api-key authentication as specified
        $response = $this->requestWithAuthStyle($url, $payloadWithClientId, 'x-api-key');
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
            'payload' => $payloadWithClientId,
        ]);

        return [
            'success' => false,
            'status' => $response->status(),
            'error' => $response->body(),
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


