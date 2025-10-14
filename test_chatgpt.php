<?php

/**
 * Test ChatGPT Integration for MigsBot
 * Run this to test the ChatGPT functionality
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\OpenAIService;

echo "=== MigsBot ChatGPT Integration Test ===\n\n";

try {
    $openai = new OpenAIService();
    
    // Check if configured
    if (!$openai->isConfigured()) {
        echo "❌ OpenAI not configured\n";
        echo "Please set OPENAI_API_KEY in your .env file\n";
        echo "Current config check:\n";
        echo "- API Key: " . (config('services.openai.key') ? 'SET' : 'NOT SET') . "\n";
        echo "- Base URL: " . config('services.openai.base_url') . "\n";
        echo "- Model: " . config('services.openai.model') . "\n";
        exit(1);
    }
    
    echo "✅ OpenAI service configured\n";
    echo "- API Key: " . (config('services.openai.key') ? 'SET (length: ' . strlen(config('services.openai.key')) . ')' : 'NOT SET') . "\n";
    echo "- Base URL: " . config('services.openai.base_url') . "\n";
    echo "- Model: " . config('services.openai.model') . "\n\n";
    
    // Test basic chat
    echo "🧪 Testing basic chat...\n";
    $response = $openai->generateMigsBotResponse("Hello, I'm looking for a wedding barong");
    
    echo "Response: " . $response . "\n\n";
    
    // Test with context
    echo "🧪 Testing with context...\n";
    $context = [
        'user' => ['name' => 'Test User', 'is_authenticated' => true],
        'available_categories' => ['Traditional Barong', 'Wedding Barong'],
        'available_fabrics' => ['Jusilyn', 'Piña Cocoon']
    ];
    
    $responseWithContext = $openai->generateMigsBotResponse("What fabrics do you have?", $context);
    
    echo "Response with context: " . $responseWithContext . "\n\n";
    
    // Test with custom system prompt
    echo "🧪 Testing with custom system prompt...\n";
    $customPrompt = "You are a fashion expert specializing in Filipino formal wear. Provide detailed styling advice and fabric recommendations. Be enthusiastic and use fashion terminology.";
    
    $customResponse = $openai->generateCustomResponse("What should I wear to a Filipino wedding?", $customPrompt);
    
    echo "Custom response: " . $customResponse . "\n\n";
    
    echo "🎉 All ChatGPT integration tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    echo "Please check your OpenAI API key and configuration\n";
}
