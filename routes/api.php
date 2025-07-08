<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "Hello Route";
});

Route::get('/hello', function () {
    return "Hello Route";
});
