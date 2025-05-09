<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckApiController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\apiProtectedRoute;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::get('checkapi', [CheckApiController::class, 'checkApi']);

Route::middleware([apiProtectedRoute::class])->group(function () { 

    #Routes for products
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{code}', [ProductController::class, 'show']);
    Route::put('products/{code}', [ProductController::class, 'update']);
    Route::delete('products/{code}', [ProductController::class, 'delete']);
    Route::post('products/{code}/publish', [ProductController::class, 'publish']);

    #Routes for auth
    Route::post('logout', [AuthController::class, 'logout']);

});