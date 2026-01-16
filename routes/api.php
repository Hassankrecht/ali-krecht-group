<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;

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

// User profile update endpoint
Route::middleware('auth:sanctum')->patch('/user', [\App\Http\Controllers\Api\UserApiController::class, 'update']);

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
Route::middleware('auth:sanctum')->get('/products', [\App\Http\Controllers\Api\ProductApiController::class, 'index']);
Route::get('/products/{id}', [\App\Http\Controllers\Api\ProductShowApiController::class, 'show']);

// Order endpoints (auth required)
Route::middleware('auth:sanctum')->get('/orders', [\App\Http\Controllers\Api\OrderApiController::class, 'index']);
Route::middleware('auth:sanctum')->get('/orders/{id}', [\App\Http\Controllers\Api\OrderShowApiController::class, 'show']);

// Authenticated user info
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Sitemap.xml
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
