<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Api\AddressApiController;
use App\Http\Controllers\Api\AppHomeSettingApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ContactApiController;
use App\Http\Controllers\Api\CouponApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\OrderShowApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ProductShowApiController;
use App\Http\Controllers\Api\PublicAssetApiController;
use App\Http\Controllers\Api\UserApiController;

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

// Public media endpoint for Flutter Web image loading
Route::get('/media/{path}', [PublicAssetApiController::class, 'show'])->where('path', '.*');

// Mobile app authentication endpoints
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/auth/google', [AuthApiController::class, 'googleLogin']);
Route::post('/auth/facebook', [AuthApiController::class, 'facebookLogin']);
Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
Route::middleware('auth:sanctum')->post('/logout', [AuthApiController::class, 'logout']);

// Category endpoints
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/parents', [CategoryApiController::class, 'parents']);
Route::get('/categories/{id}', [CategoryApiController::class, 'show']);

// Coupon endpoints
Route::get('/coupons', [CouponApiController::class, 'index']);

// Contact endpoint
Route::post('/contact', [ContactApiController::class, 'store']);

// Flutter app home/settings endpoint
Route::get('/app-home-settings', [AppHomeSettingApiController::class, 'show']);
Route::get('/app-settings', [AppHomeSettingApiController::class, 'show']);

// User profile endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserApiController::class, 'show']);
    Route::put('/profile', [UserApiController::class, 'update']);
    Route::put('/profile/password', [UserApiController::class, 'updatePassword']);
    Route::get('/user', [UserApiController::class, 'show']);
    Route::patch('/user', [UserApiController::class, 'update']);
});

// Product reviews endpoints
Route::get('/products/{id}/reviews', [\App\Http\Controllers\Api\ReviewApiController::class, 'index']);
Route::middleware('auth:sanctum')->post('/products/{id}/reviews', [\App\Http\Controllers\Api\ReviewApiController::class, 'store']);

// Cart endpoints (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [\App\Http\Controllers\Api\CartApiController::class, 'get']);
    Route::post('/cart/add', [\App\Http\Controllers\Api\CartApiController::class, 'add']);
    Route::post('/cart/remove', [\App\Http\Controllers\Api\CartApiController::class, 'remove']);
    Route::post('/cart/clear', [\App\Http\Controllers\Api\CartApiController::class, 'clear']);
});

// Product endpoints
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/popular', [ProductApiController::class, 'popular']);
Route::get('/products/featured', [ProductApiController::class, 'featured']);
Route::get('/products/{id}', [ProductShowApiController::class, 'show']);

// Order endpoints (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::post('/orders', [OrderApiController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderApiController::class, 'cancel']);
    Route::get('/orders/{id}', [OrderShowApiController::class, 'show']);
});

// Saved address endpoints (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/addresses', [AddressApiController::class, 'index']);
    Route::post('/addresses', [AddressApiController::class, 'store']);
    Route::put('/addresses/{id}', [AddressApiController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressApiController::class, 'destroy']);
    Route::put('/addresses/{id}/default', [AddressApiController::class, 'setDefault']);
});

// Sitemap.xml
Route::get('/sitemap.xml', [SitemapController::class, 'index']);



