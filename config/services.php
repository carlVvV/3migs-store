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

    // (removed) OpenAI config placeholder
    // BUX_CHECKOUT_URL=https://api.bux.ph/v1/api/sandbox/open/checkout
    // BUX_PAYMENT_BASE_URL=https://app.bux.ph/test/checkout
    
    // Bux.ph Payments
    'bux' => [
        'base_url' => env('BUX_BASE_URL', 'https://app.bux.ph/test/checkout'),
        'api_key' => env('BUX_API_KEY'),
        'secret' => env('BUX_SECRET'),
        'webhook_secret' => env('BUX_WEBHOOK_SECRET'),
        'merchant_id' => env('BUX_MERCHANT_ID'),
        'checkout_url' => env('BUX_CHECKOUT_URL', 'https://api.bux.ph/v1/api/sandbox/open/checkout'),
    ],

];
