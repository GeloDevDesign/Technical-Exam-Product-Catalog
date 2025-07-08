<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Models\Category;

Route::post('/login', [AuthController::class, 'login'])->middleware(['throttle:5,1']);
Route::post('/register', [AuthController::class, 'register'])->middleware(['guest', 'throttle:10:2']);
Route::post('/logout', [AuthController::class, 'register']);

Route::resource('product', ProductController::class)->middleware('auth:sanctum');
Route::get('/categories', [Category::class, 'index']);
