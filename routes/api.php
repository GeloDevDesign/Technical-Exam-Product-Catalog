<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return "Hello Route";
});

Route::get('/hello', function () {
    return "Hello Route";
});

Route::resource('product', ProductController::class);