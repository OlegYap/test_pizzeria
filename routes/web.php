<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));


Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('cart', CartController::class);
    Route::resource('cart-products', CartProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('cart-products', CartProductController::class);
});
