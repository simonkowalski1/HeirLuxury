<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController as FrontCategoryController;
use App\Http\Controllers\ProductController as FrontProductController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;

// Home page
Route::get('/', function () {
    return view('home');
})->name('home');

Route::view('/contact', 'contact')->name('contact');

// Catalog index
Route::get('/catalog', [FrontCategoryController::class, 'index'])
    ->name('catalog.grouped');

// Category page
Route::get('/catalog/{category}', [FrontCategoryController::class, 'show'])
    ->name('catalog.category');

// Product page
Route::get('/catalog/{category}/{productSlug}', [FrontProductController::class, 'show'])
    ->name('product.show');

// Legacy URLs → redirect to canonical catalog routes
Route::get('/categories/{category}', function ($category) {
    return redirect()->route('catalog.category', ['category' => $category], 301);
});

Route::get('/categories', function () {
    return redirect()->route('catalog.grouped');
});


// Dashboard & Profile
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class);
    });

require __DIR__ . '/auth.php';