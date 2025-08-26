<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CarModelController;
use App\Http\Controllers\Api\V1\CarController;
use App\Http\Controllers\Api\V1\ColorController;

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Cars CRUD (only for authenticated users)
        Route::apiResource('cars', CarController::class);
    });

    // Public routes for brands, models, and colors
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('car-models', CarModelController::class);
    Route::apiResource('colors', ColorController::class)->only(['index', 'show']);
});
