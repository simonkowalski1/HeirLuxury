<?php

/**
 * HeirLuxury Web Routes
 *
 * This file defines all web routes for the application. Routes are organized into:
 * - Public routes: Home, contact, catalog browsing
 * - API routes: Endpoints for AJAX/infinite scroll
 * - Auth routes: User dashboard and profile management
 * - Admin routes: Protected admin panel routes
 *
 * URL Structure:
 * - /                     → Redirects to /en
 * - /{locale}             → Home page with locale (en, pl)
 * - /{locale}/catalog     → All products (paginated)
 * - /{locale}/catalog/{category}   → Filtered by category
 * - /{locale}/catalog/{category}/{slug} → Individual product page
 * - /admin/*              → Admin panel (requires auth + admin middleware)
 */

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CategoryController as FrontCategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController as FrontProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/en');
});

/*
|--------------------------------------------------------------------------
| Localized Public Routes
|--------------------------------------------------------------------------
|
| These routes are accessible to all visitors without authentication.
| Prefixed with locale (en, pl).
|
*/

Route::prefix('{locale}')
    ->where(['locale' => 'en|pl'])
    ->middleware('locale')
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        Route::view('/contact', 'contact')->name('contact');

        /*
        |--------------------------------------------------------------------------
        | Catalog Routes
        |--------------------------------------------------------------------------
        |
        | Product catalog browsing with hierarchical category support.
        |
        | Category slug types:
        | - Gender: "women", "men" → All products for that gender
        | - Section: "women-bags", "men-shoes" → All products in that section
        | - Leaf: "louis-vuitton-women-bags" → Specific brand category
        |
        */

        // Catalog index - shows all products
        Route::get('/catalog', [FrontCategoryController::class, 'index'])
            ->name('catalog.grouped');

        // Category page - filtered by gender, section, or leaf category
        Route::get('/catalog/{category}', [FrontCategoryController::class, 'show'])
            ->name('catalog.category');

        // Individual product page
        Route::get('/catalog/{category}/{productSlug}', [FrontProductController::class, 'show'])
            ->name('product.show');
    });

/*
|--------------------------------------------------------------------------
| API Routes (AJAX) - Non-localized
|--------------------------------------------------------------------------
|
| JSON endpoints for client-side functionality like infinite scroll.
| Returns HTML fragments and pagination metadata.
|
*/

Route::post('/inquiry', [ContactController::class, 'submit'])->name('inquiry.submit');

Route::get('/api/catalog/products', [FrontCategoryController::class, 'apiProducts'])
    ->name('api.catalog.products');

Route::post('/api/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])
    ->name('wishlist.toggle');
Route::get('/api/wishlist/count', [WishlistController::class, 'count'])
    ->name('wishlist.count');
Route::get('/api/wishlist/ids', [WishlistController::class, 'ids'])
    ->name('wishlist.ids');
Route::get('/api/wishlist/items', [WishlistController::class, 'items'])
    ->name('wishlist.items');

/*
|--------------------------------------------------------------------------
| Legacy URL Redirects
|--------------------------------------------------------------------------
|
| 301 redirects from old URL structure to maintain SEO and existing links.
|
*/

Route::get('/catalog', function () {
    return redirect('/en/catalog', 301);
});

Route::get('/catalog/{category}', function ($category) {
    return redirect("/en/catalog/{$category}", 301);
});

Route::get('/categories/{category}', function ($category) {
    return redirect("/en/catalog/{$category}", 301);
});

Route::get('/categories', function () {
    return redirect('/en/catalog', 301);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
|
| Routes requiring user authentication (dashboard, profile management).
|
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
|
| Protected routes for admin users. Requires both authentication and
| the 'admin' middleware to verify admin privileges.
|
| All admin routes are prefixed with /admin and named with 'admin.' prefix.
|
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::delete('products/bulk', [AdminProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
        Route::resource('products', AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class);
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Login, registration, password reset, and email verification routes.
| Provided by Laravel Breeze.
|
*/

require __DIR__.'/auth.php';
