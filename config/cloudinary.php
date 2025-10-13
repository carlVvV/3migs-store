<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Cloudinary image upload service.
    | You can set these values in your .env file or directly here.
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', 'dc8bssolt'),
    'api_key' => env('CLOUDINARY_API_KEY', '<your_api_key>'),
    'api_secret' => env('CLOUDINARY_API_SECRET', '<your_api_secret>'),
    'secure' => env('CLOUDINARY_SECURE', true),
    'url' => env('CLOUDINARY_URL', 'cloudinary://<your_api_key>:<your_api_secret>@dc8bssolt'),
    
    /*
    |--------------------------------------------------------------------------
    | Default Upload Settings
    |--------------------------------------------------------------------------
    */
    
    'default_folder' => '3migs-products',
    'max_file_size' => 10485760, // 10MB in bytes
    'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    
    /*
    |--------------------------------------------------------------------------
    | Image Transformations
    |--------------------------------------------------------------------------
    */
    
    'transformations' => [
        'thumbnail' => [
            'width' => 300,
            'height' => 300,
            'crop' => 'fill',
            'quality' => 'auto'
        ],
        'medium' => [
            'width' => 600,
            'height' => 600,
            'crop' => 'fill',
            'quality' => 'auto'
        ],
        'large' => [
            'width' => 1200,
            'height' => 1200,
            'crop' => 'limit',
            'quality' => 'auto'
        ]
    ]
];

