<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes (PUBLIC - No Protection)
|--------------------------------------------------------------------------
| These routes are accessible without being logged in
*/

// Admin login routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (PRIVATE - Requires Admin Login)
|--------------------------------------------------------------------------
| All these routes require admin authentication
*/

Route::middleware(['admin.auth'])->group(function () {

    // ===============================
    // 1. DASHBOARD ROUTES
    // ===============================

    // Main dashboard
    Route::get('/admin', [AdminController::class, "admin_home"])->name("admin_home");

    // Authentication
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // ===============================
    // 2. PLATFORM ANALYTICS ROUTES
    // ===============================

    // Analytics dashboard with date range filtering
    Route::get('/admin/platform-statistics', [AdminController::class, 'platform_statistics'])->name('admin_platform_statistics');

    // ===============================
    // 3. CUSTOMER MANAGEMENT ROUTES
    // ===============================

    // Customer listing and search
    Route::get('/admin/customers', [AdminController::class, 'get_customers'])->name('admin_get_customers');

    // Customer details and management
    Route::get('/admin/customers/{customer}', [AdminController::class, 'show_customer'])->name('admin_show_customer');
    Route::get('/admin/customers/{customer}/edit', [AdminController::class, 'edit_customer_form'])->name('admin_edit_customer_form');
    Route::put('/admin/customers/{customer}', [AdminController::class, 'update_customer'])->name('admin_update_customer');

    // Customer status management
    Route::post('/admin/customers/{customer}/toggle-block', [AdminController::class, 'toggle_customer_block'])->name('admin_toggle_customer_block');
    Route::post('/admin/customers/{customer}/toggle-activation', [AdminController::class, 'toggle_customer_activation'])->name('admin_toggle_customer_activation');
    Route::post('/admin/customers/{customer}/toggle-verification', [AdminController::class, 'toggle_customer_verification'])->name('admin_toggle_customer_verification');

    // Customer deletion
    Route::delete('/admin/customers/{customer}', [AdminController::class, 'delete_customer'])->name('admin_delete_customer');

    // Customer transactions
    Route::post('/admin/customers/{customer}/transactions', [AdminController::class, 'add_customer_transaction'])->name('admin_add_customer_transaction');

    // Customer image delete route
    Route::post('/admin/customers/{customer}/delete-image', [AdminController::class, 'delete_customer_image'])->name('admin_delete_customer_image');

    // ===============================
    // 4. APP CONFIGURATION ROUTES
    // ===============================

    // Categories Management
    Route::get('/admin/app-config/categories', [AdminController::class, 'get_categories'])->name('admin_get_categories');
    Route::get('/admin/app-config/categories/create', [AdminController::class, 'create_category_form'])->name('admin_create_category_form');
    Route::post('/admin/app-config/categories', [AdminController::class, 'store_category'])->name('admin_store_category');
    Route::get('/admin/app-config/categories/{category}/edit', [AdminController::class, 'edit_category_form'])->name('admin_edit_category_form');
    Route::put('/admin/app-config/categories/{category}', [AdminController::class, 'update_category'])->name('admin_update_category');
    Route::delete('/admin/app-config/categories/{category}', [AdminController::class, 'delete_category'])->name('admin_delete_category');

    // Subcategories Management
    Route::get('/admin/app-config/categories/{category}/subcategories', [AdminController::class, 'get_subcategories'])->name('admin_get_subcategories');
    Route::post('/admin/app-config/categories/{category}/subcategories', [AdminController::class, 'store_subcategory'])->name('admin_store_subcategory');
    Route::put('/admin/app-config/subcategories/{subcategory}', [AdminController::class, 'update_subcategory'])->name('admin_update_subcategory');
    Route::delete('/admin/app-config/subcategories/{subcategory}', [AdminController::class, 'delete_subcategory'])->name('admin_delete_subcategory');

    // Coupons Management
    Route::get('/admin/app-config/coupons', [AdminController::class, 'get_coupons'])->name('admin_get_coupons');
    Route::get('/admin/app-config/coupons/create', [AdminController::class, 'create_coupon_form'])->name('admin_create_coupon_form');
    Route::post('/admin/app-config/coupons', [AdminController::class, 'store_coupon'])->name('admin_store_coupon');
    Route::get('/admin/app-config/coupons/{coupon}/edit', [AdminController::class, 'edit_coupon_form'])->name('admin_edit_coupon_form');
    Route::put('/admin/app-config/coupons/{coupon}', [AdminController::class, 'update_coupon'])->name('admin_update_coupon');
    Route::delete('/admin/app-config/coupons/{coupon}', [AdminController::class, 'delete_coupon'])->name('admin_delete_coupon');
    Route::post('/admin/app-config/coupons/{coupon}/toggle-status', [AdminController::class, 'toggle_coupon_status'])->name('admin_toggle_coupon_status');

    // Regional Taxes Management
    Route::get('/admin/app-config/regional-taxes', [AdminController::class, 'get_regional_taxes'])->name('admin_get_regional_taxes');
    Route::get('/admin/app-config/regional-taxes/create', [AdminController::class, 'create_regional_tax_form'])->name('admin_create_regional_tax_form');
    Route::post('/admin/app-config/regional-taxes', [AdminController::class, 'store_regional_tax'])->name('admin_store_regional_tax');
    Route::get('/admin/app-config/regional-taxes/{regionTax}/edit', [AdminController::class, 'edit_regional_tax_form'])->name('admin_edit_regional_tax_form');
    Route::put('/admin/app-config/regional-taxes/{regionTax}', [AdminController::class, 'update_regional_tax'])->name('admin_update_regional_tax');
    Route::delete('/admin/app-config/regional-taxes/{regionTax}', [AdminController::class, 'delete_regional_tax'])->name('admin_delete_regional_tax');
    Route::get('/admin/app-config/regional-taxes/calculator', [AdminController::class, 'tax_calculator'])->name('admin_tax_calculator');

    // Platform Fees Management
    Route::get('/admin/app-config/platform-fees', [AdminController::class, 'get_platform_fees'])->name('admin_get_platform_fees');
    Route::post('/admin/app-config/platform-fees', [AdminController::class, 'update_platform_fees'])->name('admin_update_platform_fees');
    Route::get('/admin/app-config/platform-fees/calculator', [AdminController::class, 'fees_calculator'])->name('admin_fees_calculator');
    Route::post('/admin/app-config/platform-fees/reset', [AdminController::class, 'reset_platform_fees'])->name('admin_reset_platform_fees');

    // Default Images Management
    Route::get('/admin/app-config/default-images', [AdminController::class, 'get_default_images'])->name('admin_get_default_images');
    Route::post('/admin/app-config/default-images', [AdminController::class, 'update_default_images'])->name('admin_update_default_images');
    Route::post('/admin/app-config/default-images/reset', [AdminController::class, 'reset_default_images'])->name('admin_reset_default_images');
    Route::post('/admin/app-config/default-images/preview', [AdminController::class, 'preview_image'])->name('admin_preview_image');

    // ===============================
    // 5. ADVERTISEMENT MANAGEMENT ROUTES
    // ===============================

    // Ads listing and search
    Route::get('/admin/app-config/ads', [AdminController::class, 'get_ads'])->name('admin_get_ads');

    // Ads creation
    Route::get('/admin/app-config/ads/create', [AdminController::class, 'create_ad_form'])->name('admin_create_ad_form');
    Route::post('/admin/app-config/ads', [AdminController::class, 'store_ad'])->name('admin_store_ad');

    // Ads editing
    Route::get('/admin/app-config/ads/{ad}/edit', [AdminController::class, 'edit_ad_form'])->name('admin_edit_ad_form');
    Route::put('/admin/app-config/ads/{ad}', [AdminController::class, 'update_ad'])->name('admin_update_ad');

    // Ads status management
    Route::post('/admin/app-config/ads/{ad}/toggle-status', [AdminController::class, 'toggle_ad_status'])->name('admin_toggle_ad_status');

    // Ads deletion
    Route::delete('/admin/app-config/ads/{ad}', [AdminController::class, 'delete_ad'])->name('admin_delete_ad');

    // Ads analytics
    Route::get('/admin/app-config/ads/{ad}/analytics', [AdminController::class, 'show_ad_analytics'])->name('admin_show_ad_analytics');

    // Ads image preview (for upload validation)
    Route::post('/admin/app-config/ads/preview-image', [AdminController::class, 'preview_ad_image'])->name('admin_preview_ad_image');
});

/*
|--------------------------------------------------------------------------
| Route Comments for Future Features
|--------------------------------------------------------------------------
| 
| PLANNED FEATURES TO ADD LATER:
| - Service Management (/admin/services/*)
| - Order Management (/admin/orders/*)
| - Ratings Management (/admin/ratings/*)
| - Withdrawal Requests (/admin/withdrawals/*)
| - Service Provider Management (/admin/service-providers/*)
|
*/