<?php

use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('customer/login', [CustomerController::class, 'login']);
Route::apiResource('customers', CustomerController::class);

Route::apiResource('users', UserController::class);
Route::post('user/login', [UserController::class, 'login']);

Route::post('/products/upload-image', [ProductController::class, 'uploadImage']);
Route::apiResource('products', ProductController::class);

Route::apiResource('orders', OrderController::class);
Route::post('orders/purchase', [OrderController::class, 'purchase']);

Route::delete('cart-items/{session_id}/{cartItem}', [CartItemController::class, 'destroy']);
Route::get('cart-items/{session_id}', [CartItemController::class, 'index']);
Route::apiResource('cart-items', CartItemController::class);



