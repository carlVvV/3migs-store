<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->baseUrl = rtrim(config('services.openai.base_url'), '/');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function chat(array $messages, array $options = []): array
    {
        $payload = array_merge([
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 400,
        ], $options);

        $response = Http::withToken($this->apiKey)
            ->acceptJson()
            ->post($this->baseUrl . '/chat/completions', $payload)
            ->throw();

        return $response->json();
    }
}


