<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Models\Admin\Admin;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Projects
|--------------------------------------------------------------------------
*/
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Language Switcher
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', function ($locale) {
    $supported = config('app.supported_locales', ['en', 'ar', 'pt']);
    if (in_array($locale, $supported)) {
        session(['app_locale' => $locale]);
    }
    return back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Authentication (for normal users)
|--------------------------------------------------------------------------
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| User Dashboard + Cart (requires login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
});

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/cart/increase/{id}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{id}', [CartController::class, 'decrease'])->name('cart.decrease');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::post('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
Route::get('/checkout/thank-you/{order}', [CheckoutController::class, 'thankYou'])->name('checkout.thankyou');
Route::get('/checkout/invoice/{order}', [CheckoutController::class, 'downloadInvoice'])->name('checkout.invoice');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // 🔐 صفحة تسجيل الدخول الخاصة بالمدير
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    // 🧱 لوحة التحكم ومساراتها (تتطلب صلاحيات مدير)
    Route::middleware('admin.auth')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // 📂 Projects CRUD
        Route::resource('projects', AdminProjectController::class);

        // ✅ حذف صورة من الجاليري
        Route::delete('project-image/{image}', [AdminProjectController::class, 'deleteImage'])
            ->name('projects.image.delete');

        // ✅ حذف الصورة الرئيسية فقط
        Route::delete('projects/{project}/mainimage', [AdminProjectController::class, 'deleteMainImage'])
            ->name('projects.mainimage.delete');
        Route::post(
            'projects/{project}/add-images',
            [AdminProjectController::class, 'addImages']
        )->name('projects.addImages');
        Route::resource('products', AdminProductController::class);

        // Delete ONLY main image
        Route::delete('products/{product}/main-image', [AdminProductController::class, 'deleteMainImage'])
            ->name('products.mainimage.delete');

        // Delete single gallery image
        Route::delete('products/gallery/{image}', [AdminProductController::class, 'deleteGalleryImage'])
            ->name('products.image.delete');

        // Add gallery images
        Route::post('products/{product}/gallery', [AdminProductController::class, 'addImages'])
            ->name('products.addImages');
        // categories
        Route::get('/categories', [App\Http\Controllers\Admin\AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [App\Http\Controllers\Admin\AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [App\Http\Controllers\Admin\AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [App\Http\Controllers\Admin\AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Filter Products by Category
        Route::get('/products/category/{id}', [App\Http\Controllers\Admin\AdminProductController::class, 'filterByCategory'])->name('products.category');
    });
});
