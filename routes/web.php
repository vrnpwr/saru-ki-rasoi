<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('items', \App\Http\Controllers\Admin\ItemController::class);
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
});

// Customer Routes
Route::get('/', function () {
    $categories = \App\Models\Category::with('items.variations.options')->get();
    return view('welcome', compact('categories'));
})->name('home');

Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::get('/cart/add/{item}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::get('/cart/decrease/{id}', [\App\Http\Controllers\CartController::class, 'decrease'])->name('cart.decrease');
Route::get('/cart/increase/{id}', [\App\Http\Controllers\CartController::class, 'increase'])->name('cart.increase');
Route::get('/cart/remove/{id}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');

Route::get('auth/google', [\App\Http\Controllers\Auth\SocialController::class, 'redirect'])->name('auth.google');
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\SocialController::class, 'callback']);

require __DIR__ . '/auth.php';
