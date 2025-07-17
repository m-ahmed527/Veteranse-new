<?php

use App\Filters\CategoryFilter;
use App\Filters\SearchFilter;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\User\AddOnController;
use App\Http\Controllers\Api\User\BookingController;
use App\Http\Controllers\Api\User\CartController;
use App\Http\Controllers\Api\User\CategoryController;
use App\Http\Controllers\Api\User\OrderController;
use App\Http\Controllers\Api\User\ProductController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\ServiceController;
use App\Http\Controllers\Api\User\WishlistController;
use App\Http\Controllers\Api\Vendor\StripeWebhookController;
use App\Http\Controllers\Api\Vendor\ChatController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\StripeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stripe\Account;

Route::get('/test', function (Request $request) {
    return "Hello, this is a test route!";
});


Route::get('/user', function (Request $request) {
    try {
        $user = $request->user()->fresh();
        $user->load('stripeAccount', 'walletTransactions');
        return responseSuccess('User found successfully', $user);
    } catch (\Exception $e) {
        return responseError('Something went wrong', 500);
    }
})->middleware('auth:sanctum');


Route::controller(AuthController::class)->group(function () {
    Route::post('/register',  'register');
    Route::post('/verify-otp',  'verifyOtp');
    Route::post('/resend-otp',  'resendOtp');
    Route::post('/login',  'login');
    Route::post('/logout',  'logout')->middleware('auth:sanctum');
    Route::post('/forgot-password',  'forgotPassword');
    Route::post('/verify-reset-token',  'verifyResetToken');
    Route::post('/reset-password',  'resetPassword');
});
Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [EmailVerificationController::class, 'resend']);

Route::middleware('auth:sanctum')->controller(ProfileController::class)->group(function () {
    Route::post('/edit/profile/{user}', 'update');
});

Route::middleware('auth:sanctum')->prefix('category')->controller(CategoryController::class)->group(function () {
    Route::get('/all-cateogries', 'index')->withoutMiddleware('is_vendor');
    Route::get('/single-category/{id}', 'show')->withoutMiddleware('is_vendor');
});
Route::middleware('auth:sanctum')->prefix('addon')->controller(AddOnController::class)->group(function () {
    Route::get('/all-addons', 'index')->withoutMiddleware('is_vendor');
    Route::get('/single-addon/{id}', 'show')->withoutMiddleware('is_vendor');
});


Route::middleware('auth:sanctum')->prefix('services')->controller(ServiceController::class)->group(function () {
    Route::get('/all-services', 'getAllServices')->withoutMiddleware('is_vendor');
    Route::get('/single-service/{id}', 'show')->withoutMiddleware('is_vendor');
});


Route::middleware('auth:sanctum')->prefix('products')->controller(ProductController::class)->group(function () {
    Route::get('/all-products', 'getAllProducts')->withoutMiddleware('is_vendor');
    Route::get('/single-product/{id}', 'show')->withoutMiddleware('is_vendor');
});


Route::middleware('auth:sanctum')->prefix('cart')->controller(CartController::class)->group(function () {
    Route::get('/get-cart', 'index');
    Route::post('/add-to-cart', 'store');
    Route::post('/update-cart', 'store');
    Route::post('/remove-from-cart', 'removeFromCart');
    Route::post('/clear-cart', 'destroy');
});

Route::middleware('auth:sanctum')->prefix('wishlist')->controller(WishlistController::class)->group(function () {
    Route::get('/get-wishlist', 'index');
    Route::post('/add-to-wishlist', 'add');
    Route::post('/remove-from-wishlist', 'remove');
    Route::post('/clear-wishlist', 'clear');
});


Route::middleware('auth:sanctum')->prefix('booking')->controller(BookingController::class)->group(function () {
    Route::get('/get-all-bookings', 'getAllBookings');
    Route::get('/get-bookings', 'index');
    Route::post('/make-booking', 'store');
    Route::post('/make-booking-wallet', 'craeteBookingWithWallet');
});

Route::middleware('auth:sanctum')->prefix('tax')->controller(TaxController::class)->group(function () {
    Route::get('/all-taxes', 'index');
    Route::post('/create-tax', 'store');
    Route::get('/single-tax/{id}', 'show');
    Route::put('/update-tax/{id}', 'update');
    Route::delete('/delete-tax/{id}', 'destroy');
});

Route::middleware('auth:sanctum')->prefix('order')->controller(OrderController::class)->group(function () {
    Route::get('/get-orders', 'index');
    Route::post('/make-order', 'store');
    Route::post('/make-order-wallet', 'craeteOrderWithWallet');
});

Route::get('/search', function (Request $request) {
    try {
        // dd($request->all());
        $filters = [
            SearchFilter::class,
        ];

        $products = Product::with(['category'])->filter($filters)->get();

        $services = Service::with(['category', 'addOns'])->filter($filters)->get();

        $results = $products->merge($services);
        // dd($results);
        if ($results->isEmpty()) {
            return responseError('No results found', 404);
        }
        return responseSuccess('Search results retrieved successfully', $results);
    } catch (\Exception $e) {
        return responseError('Something went wrong', 500);
    }
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::post('/vendor/chat/send', [ChatController::class, 'sendMessage']);
Route::post('/vendor/chat/reset-unread', [ChatController::class, 'resetUnreadCount']);
