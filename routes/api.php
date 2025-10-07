<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::middleware(['auth:api', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);

    Route::resource('carts', CartController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('cart-products', CartProductController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'store', 'destroy']);
    Route::resource('order-products', OrderProductController::class)->only(['index', 'show', 'store']);
});

Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('carts', CartController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('order-products', OrderProductController::class);
});
