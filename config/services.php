<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // OpenAI ChatGPT Configuration
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 500),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],
    
    // Bux.ph Payments
    'bux' => [
        'base_url' => env('BUX_BASE_URL', 'https://app.bux.ph/test/checkout'),
        'api_key' => env('BUX_API_KEY'),
        'secret' => env('BUX_SECRET'),
        'webhook_secret' => env('BUX_SECRET'),
        'merchant_id' => env('BUX_MERCHANT_ID'),
        'checkout_url' => env('BUX_CHECKOUT_URL', 'https://api.bux.ph/v1/api/sandbox/open/checkout'),
    ],

    // Google Account Signin Config
    'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],

    'veriff' => [
        'api_key' => env('VERIFF_API_KEY'),
        'secret_key' => env('VERIFF_SECRET_KEY'),
    ],

];
