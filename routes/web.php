<?php

use Illuminate\Support\Facades\Route;

// ---------- Front-end controllers ----------
use App\Http\Controllers\CategoryController as FrontCategoryController;
use App\Http\Controllers\ProductController as FrontProductController;

// ---------- Breeze profile/dashboard ----------
use App\Http\Controllers\ProfileController;

// ---------- Admin controllers ----------
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;

/*
|--------------------------------------------------------------------------
| PUBLIC SITE ROUTES (HeirLuxury catalog + homepage)
|--------------------------------------------------------------------------
*/

// Home page (hero + New Arrivals)
Route::get('/', function () {
    return view('home'); // resources/views/home.blade.php
})->name('home');

// Simple contact page (in addition to the modal)
Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| Catalog + category routes
| We standardize on categorySlug (string) == products.category_slug
|--------------------------------------------------------------------------
*/

// Catalog index (“Catalog” button in navbar)
Route::get('/catalog', [FrontCategoryController::class, 'index'])
    ->name('catalog.grouped');

// Canonical category page (mega menu + sidenav should link here)
Route::get('/catalog/{categorySlug}', [FrontCategoryController::class, 'show'])
    ->name('catalog.category');

// Product page (category + product slug)
Route::get('/catalog/{categorySlug}/{productSlug}', [FrontProductController::class, 'show'])
    ->name('product.show');

// Legacy old-style URL still works: /categories/louis-vuitton-women-bags
Route::get('/categories/{categorySlug}', [FrontCategoryController::class, 'show'])
    ->name('categories.show');

// Optional legacy alias for /categories → /catalog
Route::get('/categories', function () {
    return redirect()->route('catalog.grouped');
});

/*
|--------------------------------------------------------------------------
| BREEZE USER DASHBOARD + PROFILE
|--------------------------------------------------------------------------
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
| ADMIN PANEL ROUTES (/admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('products', AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class);
    });

require __DIR__ . '/auth.php';
