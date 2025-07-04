<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TagController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'getStatistics'])->name('stats');

    // Categories Management
    Route::apiResource('categories', CategoryController::class);
    Route::get('/categories/parent/list', [CategoryController::class, 'getParentCategories'])->name('categories.parents');

    // Products Management
    Route::apiResource('products', ProductController::class);
    Route::patch('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
    Route::patch('/products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.update-stock');

    // Orders Management
    Route::apiResource('orders', OrderController::class)->except(['store', 'destroy']);
    Route::patch('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment-status');
    Route::get('/orders/statistics', [OrderController::class, 'getStatistics'])->name('orders.statistics');
    Route::get('/orders/export', [OrderController::class, 'exportOrders'])->name('orders.export');

    // Coupons Management
    Route::apiResource('coupons', CouponController::class);

    // Users Management
    Route::apiResource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Tags Management
    Route::apiResource('tags', TagController::class);

    // Reports
    Route::get('/reports/sales', [DashboardController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/products', [DashboardController::class, 'productsReport'])->name('reports.products');
    Route::get('/reports/customers', [DashboardController::class, 'customersReport'])->name('reports.customers');
});
