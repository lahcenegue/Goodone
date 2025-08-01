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

    // Dashboard and Authentication
    Route::get('/admin', [AdminController::class, "admin_home"])->name("admin_home");
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Services Management
    Route::get('/admin/services', [AdminController::class, "get_services"])->name("admin_get_services");
    Route::post('/admin/services/{service}', [AdminController::class, "get_service"])->name("admin_get_service");
    Route::get('/admin/services/{service}/toggle_activation', [AdminController::class, "toggle_service_activation"])->name("admin_toggle_service_activation");
    Route::get('/admin/services/{service}/delete', [AdminController::class, "delete_service"])->name("admin_delete_service");

    // Ratings Management
    Route::get('/admin/services/{service}/ratings', [AdminController::class, "get_service_ratings"])->name("admin_get_service_ratings");
    Route::get('/admin/ratings/{rating}/delete', [AdminController::class, "delete_rating"])->name("admin_delete_rating");

    // Coupons Management
    Route::get('/admin/coupon', [AdminController::class, "create_coupon"])->name("admin_create_coupon");
    Route::post('/admin/coupon', [AdminController::class, "store_coupon"])->name("admin_store_coupon");
    Route::get('/admin/coupon/delete', [AdminController::class, "delete_coupon"])->name("admin_delete_coupon");

    // Withdrawals Management
    Route::get('/admin/withdrawals', [AdminController::class, "withdraw_requests"])->name("admin_withdraw_requests");
    Route::get('/admin/withdrawals/{withdraw_request}/accept', [AdminController::class, "accept_withdraw_request"])->name("admin_accept_withdraw_requests");
    Route::get('/admin/withdrawals/{withdraw_request}/reject', [AdminController::class, "reject_withdraw_request"])->name("admin_reject_withdraw_requests");

    // Categories Management
    Route::get('/admin/category', [AdminController::class, "create_category"])->name("admin_create_category");
    Route::post('/admin/category', [AdminController::class, "store_category"])->name("admin_store_category");
    Route::get('/admin/category/{category}', [AdminController::class, "edit_category"])->name("admin_edit_category");
    Route::post('/admin/category/{category}', [AdminController::class, "update_category"])->name("admin_update_category");
    Route::get('/admin/category/{category}/delete', [AdminController::class, "delete_category"])->name("admin_delete_category");

    // Subcategories Management
    Route::get('/admin/subcategory', [AdminController::class, "create_subcategory"])->name("admin_create_subcategory");
    Route::get('/admin/subcategory/{subcategory}', [AdminController::class, "edit_subcategory"])->name("admin_edit_subcategory");
    Route::post('/admin/subcategory/{subcategory}', [AdminController::class, "update_subcategory"])->name("admin_update_subcategory");
    Route::post('/admin/subcategory', [AdminController::class, "store_subcategory"])->name("admin_store_subcategory");
    Route::get('/admin/subcategory/{subcategory}/delete', [AdminController::class, "delete_subcategory"])->name("admin_delete_subcategory");

    // Settings Management
    Route::get('/admin/settings', [AdminController::class, "get_app_settings"])->name("admin_get_app_settings");
    Route::post('/admin/settings/edit', [AdminController::class, "edit_app_settings"])->name("admin_edit_app_settings");

    // Default Images Management
    Route::get('/admin/default_images', [AdminController::class, "get_default_images"])->name("admin_get_default_images");
    Route::post('/admin/default_images/edit', [AdminController::class, "edit_default_images"])->name("admin_edit_default_images");

    // Region Taxes Management
    Route::get('/admin/region_taxes', [AdminController::class, "create_region_tax"])->name("admin_create_region_tax");
    Route::post('/admin/region_taxes', [AdminController::class, "store_region_tax"])->name("admin_store_region_tax");
    Route::get('/admin/region_taxes/delete', [AdminController::class, "delete_region_tax"])->name("admin_delete_region_tax");
    Route::post('/admin/region_taxes/{region}', [AdminController::class, "edit_region_tax"])->name("admin_edit_region_tax")->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

    // Users Management
    Route::get('/admin/get_service_providers', [AdminController::class, "get_service_providers"])->name("admin_get_service_providers");
    Route::get('/admin/users', [AdminController::class, "get_users"])->name("admin_get_users");
    Route::get('users/{user}', [AdminController::class, "get_user"])->name("admin_get_user");
    Route::get('users/{user}/block', [AdminController::class, "block_user"])->name("admin_block_user");
    Route::get('users/{user}/unblock', [AdminController::class, "unblock_user"])->name("admin_unblock_user");
    Route::get('users/{user}/activate', [AdminController::class, "activate_user"])->name("admin_activate_user");
    Route::get('users/{user}/deactivate', [AdminController::class, "deactivate_user"])->name("admin_deactivate_user");
    Route::post('users/{user}/edit', [AdminController::class, "edit_user"])->name("admin_edit_user");

    // Orders and Transactions
    Route::get('/admin/orders', [AdminController::class, "get_orders"])->name("admin_get_orders");
    Route::get('/admin/transactions/{user}', [AdminController::class, "get_transactions"])->name("admin_get_transactions");
    Route::get('/admin/platform-statistics', [AdminController::class, 'platform_statistics'])->name('admin.platform_statistics');
});
