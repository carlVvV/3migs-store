<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

// Redirect /homepage to root for consistency
Route::redirect('/homepage', '/', 301);
Route::redirect('/Homepage', '/', 301);

// Homepage and main pages
Route::get('/test-notifications', function () {
    return view('test-notifications');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/test-home', [HomeController::class, 'testHome'])->name('test-home');
Route::get('/simple-home', [HomeController::class, 'simpleHome'])->name('simple-home');
Route::get('/no-includes-home', [HomeController::class, 'noIncludesHome'])->name('no-includes-home');
Route::get('/header-test-home', [HomeController::class, 'headerTestHome'])->name('header-test-home');
Route::get('/categories-test-home', [HomeController::class, 'categoriesTestHome'])->name('categories-test-home');
Route::get('/notification-test-home', [HomeController::class, 'notificationTestHome'])->name('notification-test-home');
Route::get('/migsbot-test-home', [HomeController::class, 'migsbotTestHome'])->name('migsbot-test-home');
Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
Route::get('/custom-design', [HomeController::class, 'customDesign'])->name('custom-design');
Route::view('/processing-order', 'processing-order')->name('processing-order');
Route::get('/product/{slug}', [HomeController::class, 'productDetails'])->name('product.details');

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category.show');
Route::get('/category/{slug}/products', [CategoryController::class, 'products'])->name('category.products');

// ============================================================================
// AUTHENTICATED ROUTES
// ============================================================================

Route::middleware('auth')->group(function () {
    // User account pages
    Route::get('/wishlist', [HomeController::class, 'wishlist'])->name('wishlist');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('/orders', [HomeController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [HomeController::class, 'orderDetails'])->name('orders.details');
    
    // Account management routes
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::post('/account/profile/update', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/password/update', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/account/notifications/update', [AccountController::class, 'updateNotifications'])->name('account.notifications.update');
    // Send OTP to email for password reset (authenticated shortcut)
    Route::post('/account/password/send-otp', [\App\Http\Controllers\PasswordResetController::class, 'sendOtpForAuthenticatedUser'])->name('account.password.send-otp');
});

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);

// Password reset (OTP-based)
Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
// Provide Breeze-compatible alias if templates use password.request
Route::get('/password/forgot', function () {
    return redirect()->route('password.forgot');
})->name('password.request');

Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendOtp'])->name('password.email');
Route::get('/verify-code', [\App\Http\Controllers\PasswordResetController::class, 'showVerifyForm'])->name('password.verify');
Route::post('/verify-code', [\App\Http\Controllers\PasswordResetController::class, 'verifyOtp'])->name('password.verify.submit');
Route::get('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.update');

// Google OAuth (Socialite) routes
Route::get('/auth/google/redirect', [\App\Http\Controllers\AuthController::class, 'googleRedirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\AuthController::class, 'googleCallback'])->name('auth.google.callback');

// ============================================================================
// ADMIN ROUTES
// ============================================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales', [AdminController::class, 'sales'])->name('sales');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/print', [AdminController::class, 'reportsPrint'])->name('reports.print');
    // Helper route to seed a test delivered order for analytics
    Route::get('/reports/seed', [AdminController::class, 'seedTestDelivery'])->name('reports.seed');
    // Seed multiple random delivered orders across months (for chart testing)
    Route::get('/reports/seed-random', [AdminController::class, 'seedRandomOrders'])->name('reports.seed.random');

    // Seed random test products (clears existing products first)
    Route::get('/products/seed', [AdminController::class, 'seedTestProducts'])->name('products.seed');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/change-password', [AdminController::class, 'changePassword'])->name('settings.change-password');
    
    // Low Stock Notifications
    Route::post('/notifications/{id}/resolve', [AdminController::class, 'resolveNotification'])->name('notifications.resolve');
    
    // Barong Products Management (Replacing old products system)
    Route::get('/products', [AdminController::class, 'barongProducts'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createBarongProduct'])->name('products.create');
    Route::get('/products/{id}/edit', [AdminController::class, 'editBarongProduct'])->name('products.edit');
    
    // Deleted Items Management
    Route::get('/deleted-items', [AdminController::class, 'deletedItems'])->name('deleted-items');
    Route::post('/products/{id}/restore', [AdminController::class, 'restoreProduct'])->name('products.restore');
    Route::delete('/products/{id}/force-delete', [AdminController::class, 'forceDeleteProduct'])->name('products.force-delete');
    
    // Admin API routes
    Route::post('/products', [AdminController::class, 'storeBarongProduct'])->name('products.store');
    Route::put('/products/{id}', [AdminController::class, 'updateBarongProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteBarongProduct'])->name('products.delete');
});

// ============================================================================
// API ROUTES
// ============================================================================

Route::prefix('api')->group(function () {
    
    // Authentication API
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware('auth');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->middleware('auth');

    // Include other API routes
    require __DIR__.'/api.php';
});

// Include Laravel Breeze auth routes
require __DIR__.'/auth.php';