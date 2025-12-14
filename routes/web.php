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
 * - /                     → Home page
 * - /catalog              → All products (paginated)
 * - /catalog/{category}   → Filtered by category (supports gender, section, or leaf)
 * - /catalog/{category}/{slug} → Individual product page
 * - /admin/*              → Admin panel (requires auth + admin middleware)
 */

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController as FrontCategoryController;
use App\Http\Controllers\ProductController as FrontProductController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| These routes are accessible to all visitors without authentication.
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

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
| @see \App\Http\Controllers\CategoryController For category resolution logic
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

/*
|--------------------------------------------------------------------------
| API Routes (AJAX)
|--------------------------------------------------------------------------
|
| JSON endpoints for client-side functionality like infinite scroll.
| Returns HTML fragments and pagination metadata.
|
*/

Route::get('/api/catalog/products', [FrontCategoryController::class, 'apiProducts'])
    ->name('api.catalog.products');

/*
|--------------------------------------------------------------------------
| Legacy URL Redirects
|--------------------------------------------------------------------------
|
| 301 redirects from old URL structure to maintain SEO and existing links.
| Old: /categories/{slug} → New: /catalog/{slug}
|
*/

Route::get('/categories/{category}', function ($category) {
    return redirect()->route('catalog.category', ['category' => $category], 301);
});

Route::get('/categories', function () {
    return redirect()->route('catalog.grouped');
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

require __DIR__ . '/auth.php';