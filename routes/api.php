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
use App\Http\Controllers\Api\V1\IdDocumentController;
use App\Http\Controllers\Api\V1\VeriffSessionController;
use App\Http\Controllers\Api\VeriffWebhookController;

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
    
    // Review routes (public)
    Route::get('/products/{productId}/reviews', [\App\Http\Controllers\Api\V1\ReviewController::class, 'getProductReviews']);
    Route::get('/products/{productId}/rating-distribution', [\App\Http\Controllers\Api\V1\ReviewController::class, 'getRatingDistribution']);
    
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
    
    // Custom Design Orders (public endpoints for guest users)
    Route::prefix('custom-design-orders')->group(function () {
        Route::post('/', [\App\Http\Controllers\CustomDesignOrderController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\CustomDesignOrderController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\CustomDesignOrderController::class, 'update']);
        Route::post('/{id}/bux-checkout', [\App\Http\Controllers\CustomDesignOrderController::class, 'buxCheckout']);
    });
});

// PSGC API endpoints for Philippine address data (public, no auth required)
Route::prefix('v1/psgc')->withoutMiddleware([
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
])->group(function () {
    Route::get('/regions', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getRegions']);
    Route::get('/regions/{regionCode}/provinces', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getProvincesByRegion']);
    Route::get('/regions/{regionCode}/cities', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getCitiesByRegion']);
    Route::get('/provinces/{provinceCode}/cities', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getCitiesByProvince']);
    Route::get('/cities/{cityCode}/barangays', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getBarangaysByCity']);
    Route::get('/cities/{code}', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getCity']);
    Route::get('/barangays/{code}', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getBarangay']);
    Route::get('/provinces/{code}', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getProvince']);
    Route::get('/regions/{regionCode}/data', [\App\Http\Controllers\Api\V1\PSGCController::class, 'getRegionData']);
    Route::post('/test', [\App\Http\Controllers\Api\V1\PSGCController::class, 'testPost']);
    Route::get('/search/city', [\App\Http\Controllers\Api\V1\PSGCController::class, 'searchCity']);
    Route::get('/search/barangay', [\App\Http\Controllers\Api\V1\PSGCController::class, 'searchBarangay']);
    Route::get('/test-service', [\App\Http\Controllers\Api\V1\PSGCController::class, 'testService']); // Added for testing PSGC service
    Route::get('/debug-manila', [\App\Http\Controllers\Api\V1\PSGCController::class, 'debugManila']); // Debug endpoint
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

    // ID document verification routes
    Route::prefix('id-documents')->group(function () {
        Route::get('/', [IdDocumentController::class, 'index']);
        Route::delete('/{id}', [IdDocumentController::class, 'destroy']);
    });

    Route::post('/veriff-session', [VeriffSessionController::class, 'store']);

    // Review routes
    Route::prefix('reviews')->group(function () {
        Route::post('/', [\App\Http\Controllers\Api\V1\ReviewController::class, 'store']);
        Route::get('/user', [\App\Http\Controllers\Api\V1\ReviewController::class, 'getUserReviews']);
        Route::post('/can-review', [\App\Http\Controllers\Api\V1\ReviewController::class, 'canReview']);
    });

    // Payments (authenticated utilities)
    Route::post('/payments/bux/test-webhook', [OrderController::class, 'testBuxWebhook']);
    
    // (Wishlist routes moved to public v1 group)
    
    // Cart sync route (for authenticated users)
    Route::post('/cart/sync', [CartController::class, 'sync']);
    
    // Custom Design Orders (authenticated-only routes)
    Route::prefix('custom-design-orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\CustomDesignOrderController::class, 'index']); // List user's orders
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

Route::post('/veriff-webhook', [VeriffWebhookController::class, 'handle']);