<?php

use App\Http\Controllers\Api\Vendor\BookingController;
use App\Http\Controllers\Api\Vendor\OrderController;
use App\Http\Controllers\Api\Vendor\PlanController;
use App\Http\Controllers\Api\Vendor\ProductController;
use App\Http\Controllers\Api\Vendor\ServiceController;
use App\Http\Controllers\Api\Vendor\StoreController;
use App\Http\Controllers\Api\Vendor\StripeAccountController;
use App\Http\Controllers\Api\Vendor\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::prefix('vendor')->group(function () {


    Route::prefix('store')->controller(StoreController::class)->group(function () {
        Route::post('/update/{vendor}', 'update');
    });


    Route::middleware('is_subscribed')->prefix('services')->controller(ServiceController::class)->group(function () {
        Route::get('/services-of-vendor', 'index');
        // Route::get('/all-services', 'getAllServices')->withoutMiddleware('is_vendor');
        Route::post('/store', 'store');
        Route::get('/single-service/{id}', 'show');
        Route::post('/update/{service}', 'update');
        Route::post('/update-status/{service}', 'updateStatus');
    });


    Route::middleware('is_subscribed')->prefix('product')->controller(ProductController::class)->group(function () {
        Route::get('/products-of-vendor', 'index');
        // Route::get('/all-products', 'getAllProducts')->withoutMiddleware('is_vendor');
        Route::post('/store', 'store');
        Route::get('/single-product/{id}', 'show');
        Route::post('/update/{product}', 'update');
        Route::post('/update-status/{product}', 'updateStatus');
        Route::post('/delete/{product}', 'destroy');
    });

    Route::prefix('subscription')->controller(SubscriptionController::class)->group(function () {
        Route::post('/subscribe', 'subscribe');
        Route::post('/subscribe-wallet', 'subscribeWithWallet');
        Route::post('/cancel-subscription', 'cancelSubscription');
        Route::post('/resume-subscription', 'resumeSubscription');
    });

    Route::prefix('plan')->controller(PlanController::class)->group(function () {
        Route::get('/all-plans', 'index');
        Route::post('/store', 'store');
    });

    Route::prefix('stripe-account')->controller(StripeAccountController::class)->group(function () {
        Route::get('/connect', 'startOnBoardProcess');
        Route::get('/connect/{account_id}/return', 'returnFromOnBoardProcess')->withoutMiddleware(['auth:sanctum', 'is_vendor']);
        Route::post('/connect/delete-account/{account_id}', 'deleteStripeAccount');
    });

    Route::prefix('booking')->controller(BookingController::class)->group(function () {
        Route::get('/bookings-of-vendor', 'index');
    });

    Route::prefix('order')->controller(OrderController::class)->group(function () {
        Route::get('/orders-of-vendor', 'index');
    });
});
