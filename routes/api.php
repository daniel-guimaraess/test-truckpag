<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{code}', [ProductController::class, 'show']);
Route::put('products/{code}', [ProductController::class, 'update']);
Route::delete('products/{code}', [ProductController::class, 'delete']);