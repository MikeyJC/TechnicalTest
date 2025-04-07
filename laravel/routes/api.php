<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceProductController;

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/service-products', [ServiceProductController::class, 'index']);
Route::get('/service-products/by-service/{id}', [ServiceProductController::class, 'byServiceId']);
