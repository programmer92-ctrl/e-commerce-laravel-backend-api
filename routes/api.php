<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/orders/all', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::post('/order/add', [OrderController::class, 'store']);
    Route::post('/order/update/{id}', [OrderController::class, 'update']);
    Route::delete('/order/delete/{id}', [OrderController::class, 'delete']);
    Route::get('/orders/user/{id}', [OrderController::class, 'getOrderItemsForUser']);

    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::post('/category/add', [CategoryController::class, 'store']);
    Route::get('/category/all', [CategoryController::class, 'index']);
    Route::post('/category/delete\{id}', [CategoryController::class, 'delete']);

    Route::get('products/all', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::get('/products/category/{category_name}', [ProductController::class, 'getProductsByCategory']);
    Route::post('/products/add', [ProductController::class, 'store']);
    Route::post('products/search', [ProductController::class, 'searchProducts']);

    Route::get('cart/user/all', [CartController::class, 'index']);
    Route::get('cart/{cartId}', [CartController::class, 'show']);
    Route::post('cart/add', [CartController::class, 'store']);
    Route::post('cart/update/{productId}', [CartController::class, 'update']);
    Route::delete('cart/{productId}', [CartController::class, 'delete']);
    
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
