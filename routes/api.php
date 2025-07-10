<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SaleController;
use App\Http\Controllers\HealthController;

Route::get('/health', [HealthController::class, 'index']);
Route::apiResource('sales', SaleController::class);
