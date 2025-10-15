<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\WishlistController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\MigsBotController;
use App\Http\Controllers\Api\V1\CustomDesignController;
use App\Http\Controllers\Api\V1\ImageUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================================
// PUBLIC API ROUTES
// ============================================================================

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// Authentication routes
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Product routes (public)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/new-arrivals', [ProductController::class, 'newArrivals']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/products/category/{categorySlug}', [ProductController::class, 'byCategory']);
    
    // Cart routes (session-based, no auth required)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/remove', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::get('/cart/summary', [CartController::class, 'summary']);
    Route::get('/cart/count', [CartController::class, 'count']);
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);

    // MigsBot public chat endpoint
    Route::post('/migsbot/chat', [MigsBotController::class, 'chat']);
    
    // Custom Design routes (public)
    Route::prefix('custom-design')->group(function () {
        Route::post('/add-to-cart', [CustomDesignController::class, 'addToCart']);
        Route::post('/get-pricing', [CustomDesignController::class, 'getPricing']);
    });

    // Image Upload routes (public for now, can be restricted later)
    Route::prefix('images')->group(function () {
        Route::post('/upload', [ImageUploadController::class, 'uploadSingle']);
        Route::post('/upload-multiple', [ImageUploadController::class, 'uploadMultiple']);
        Route::delete('/delete', [ImageUploadController::class, 'delete']);
        Route::get('/transformations', [ImageUploadController::class, 'getTransformations']);
    });

    // Product data routes (public endpoints for real-time updates)
    Route::prefix('product-data')->group(function () {
        Route::get('/{slug}', [\App\Http\Controllers\Api\V1\ProductDataController::class, 'getProductBySlug']);
        Route::get('/{slug}/size-stocks', [\App\Http\Controllers\Api\V1\ProductDataController::class, 'getProductSizeStocks']);
    });

    // Wishlist routes (public endpoints, controller enforces auth via session)
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/add', [WishlistController::class, 'add']);
        Route::post('/remove-by-product', [WishlistController::class, 'removeByProduct']);
        Route::delete('/remove/{id}', [WishlistController::class, 'remove']);
        Route::delete('/clear', [WishlistController::class, 'clear']);
        Route::post('/check', [WishlistController::class, 'check']);
        Route::get('/count', [WishlistController::class, 'count']);
    });

    // Orders: lightweight read-only detail for UI that relies on session auth
    // Controller already restricts to the logged-in user's orders
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    
    // Addresses (authenticated-only for mutations; reads allowed when logged in)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/addresses', [\App\Http\Controllers\Api\V1\AddressController::class, 'index']);
        Route::post('/addresses', [\App\Http\Controllers\Api\V1\AddressController::class, 'store']);
        Route::put('/addresses/{id}', [\App\Http\Controllers\Api\V1\AddressController::class, 'update']);
        Route::delete('/addresses/{id}', [\App\Http\Controllers\Api\V1\AddressController::class, 'destroy']);
        Route::post('/addresses/{id}/make-default', [\App\Http\Controllers\Api\V1\AddressController::class, 'makeDefault']);
    });
    // Expose order creation and Bux checkout for session-auth flows (no Sanctum token required)
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{id}/bux-checkout', [OrderController::class, 'buxCheckout']);
});

// ============================================================================
// AUTHENTICATED API ROUTES
// ============================================================================

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // Profile management routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'getProfile']);
        Route::put('/update', [ProfileController::class, 'updateProfile']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
        Route::get('/orders', [ProfileController::class, 'getOrders']);
        Route::get('/wishlist', [ProfileController::class, 'getWishlist']);
        Route::put('/notifications', [ProfileController::class, 'updateNotificationPreferences']);
    });
    
    // Order management routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/cancel', [OrderController::class, 'cancel']);
        Route::post('/{id}/bux-checkout', [OrderController::class, 'buxCheckout']);
        Route::post('/{id}/update-payment-status', [OrderController::class, 'updatePaymentStatus']);
        Route::post('/seed/sample', [OrderController::class, 'seedSample']);
        // Simple GET alias for seeding in local/testing
        Route::get('/seed/sample', [OrderController::class, 'seedSample']);
    });

    // Payments (authenticated utilities)
    Route::post('/payments/bux/test-webhook', [OrderController::class, 'testBuxWebhook']);
    
    // (Wishlist routes moved to public v1 group)
    
    // Cart sync route (for authenticated users)
    Route::post('/cart/sync', [CartController::class, 'sync']);
    
            // Custom Design Orders
            Route::prefix('custom-design-orders')->group(function () {
                Route::get('/', [\App\Http\Controllers\CustomDesignOrderController::class, 'index']);
                Route::get('/{id}', [\App\Http\Controllers\CustomDesignOrderController::class, 'show']);
                Route::post('/', [\App\Http\Controllers\CustomDesignOrderController::class, 'store']);
                Route::put('/{id}', [\App\Http\Controllers\CustomDesignOrderController::class, 'update']);
            });
});

// Public payments webhook endpoints (no auth, no CSRF)
Route::prefix('v1')->group(function () {
    Route::post('/payments/bux/webhook', [OrderController::class, 'buxWebhook'])
        ->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
});