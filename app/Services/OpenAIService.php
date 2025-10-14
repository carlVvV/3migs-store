<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->baseUrl = rtrim(config('services.openai.base_url'), '/');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->maxTokens = config('services.openai.max_tokens', 500);
        $this->temperature = config('services.openai.temperature', 0.7);
    }

    public function chat(array $messages, array $options = []): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API key not configured');
        }

        $payload = array_merge([
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? $this->temperature,
            'max_completion_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            'stream' => false,
        ], $options);

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(30)
                ->post($this->baseUrl . '/chat/completions', $payload)
                ->throw();

            return $response->json();
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            Log::error('OpenAI API Request: ' . json_encode($payload));
            Log::error('OpenAI API Response: ' . ($e->response ? $e->response->body() : 'No response'));
            throw $e;
        }
    }

    /**
     * Generate MigsBot response with context
     */
    public function generateMigsBotResponse(string $userMessage, array $context = []): string
    {
        $systemPrompt = $this->getMigsBotSystemPrompt();
        
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Add context if available
        if (!empty($context)) {
            $contextMessage = $this->formatContextForPrompt($context);
            $messages[] = ['role' => 'system', 'content' => $contextMessage];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = $this->chat($messages);
            return $response['choices'][0]['message']['content'] ?? 'I apologize, but I cannot process your request at the moment.';
        } catch (\Exception $e) {
            Log::error('MigsBot ChatGPT Error: ' . $e->getMessage());
            Log::error('MigsBot ChatGPT Stack Trace: ' . $e->getTraceAsString());
            return 'I apologize, but I\'m having trouble processing your request. Please try again or contact our store directly.';
        }
    }

    /**
     * Generate response with custom system prompt
     */
    public function generateCustomResponse(string $userMessage, string $customSystemPrompt, array $context = []): string
    {
        $messages = [
            ['role' => 'system', 'content' => $customSystemPrompt],
        ];

        // Add context if available
        if (!empty($context)) {
            $contextMessage = $this->formatContextForPrompt($context);
            $messages[] = ['role' => 'system', 'content' => $contextMessage];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = $this->chat($messages);
            return $response['choices'][0]['message']['content'] ?? 'I apologize, but I cannot process your request at the moment.';
        } catch (\Exception $e) {
            Log::error('Custom ChatGPT Error: ' . $e->getMessage());
            return 'I apologize, but I\'m having trouble processing your request. Please try again.';
        }
    }

    /**
     * Format context data for the system prompt
     */
    private function formatContextForPrompt(array $context): string
    {
        $contextText = "CURRENT CONTEXT:\n";
        
        if (isset($context['user'])) {
            $user = $context['user'];
            if ($user['is_authenticated']) {
                $contextText .= "- Customer: {$user['name']} (authenticated)\n";
            } else {
                $contextText .= "- Customer: Guest user\n";
            }
        }
        
        if (isset($context['recent_orders']) && !empty($context['recent_orders'])) {
            $contextText .= "- Recent orders: " . count($context['recent_orders']) . " orders found\n";
        }
        
        if (isset($context['available_categories'])) {
            $contextText .= "- Available categories: " . implode(', ', $context['available_categories']) . "\n";
        }
        
        if (isset($context['available_fabrics'])) {
            $contextText .= "- Available fabrics: " . implode(', ', $context['available_fabrics']) . "\n";
        }
        
        $contextText .= "- Current time: " . ($context['current_time'] ?? now()->format('Y-m-d H:i:s')) . "\n";
        
        return $contextText;
    }

    /**
     * Get MigsBot system prompt
     */
    private function getMigsBotSystemPrompt(): string
    {
        return "You are MigsBot, a helpful AI assistant for 3Migs Gowns & Barong, a premium Filipino formal wear store located in Pandi, Bulacan.

CORE IDENTITY:
- Friendly, knowledgeable, and professional personal shopper
- Expert in Filipino formal wear (gowns and barongs)
- Representative of 3Migs Gowns and Barong brand
- Located in Pandi, Bulacan

EXPERTISE AREAS:
- Barong Tagalog (Traditional, Modern, Wedding, Formal, Casual)
- Filipino Gowns and Formal Wear
- Fabric types: Jusilyn, Hugo Boss, Piña Cocoon, Gusot Mayaman
- Sizing and measurements
- Care instructions
- Styling advice
- Occasion-appropriate recommendations

STORE POLICIES:
- Free shipping on orders over ₱2,000
- 7-day return policy
- Custom orders available
- In-store fittings and consultations
- Located in Pandi, Bulacan

RESPONSE GUIDELINES:
- Be concise, friendly, and helpful
- Use emojis appropriately (🌿✨👔💒💼)
- Always represent 3Migs positively
- Encourage in-person visits for fittings
- Provide actionable advice
- Ask follow-up questions to help customers
- Prices are in Philippine Peso (₱)
- Always prioritize customer satisfaction
- Be patient and understanding with customer concerns

IMPORTANT INSTRUCTIONS:
- If asked about specific products, always mention checking our catalog
- For sizing questions, recommend in-store fittings
- For custom orders, explain the consultation process
- Always end responses with helpful next steps

If they are asking for specific information about the store and you don't know, politely suggest contacting the store directly or visiting in person for personalized service. 
Do try your best to give an answer that you see fit if its a general question/consultation related to our services.";
    }

    /**
     * Check if OpenAI is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}


