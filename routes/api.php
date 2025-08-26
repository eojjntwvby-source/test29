<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Include versioned API routes
require __DIR__ . '/api_v1.php';

// Default redirect to latest version
Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Car Management API',
        'version' => 'v1',
        'endpoints' => [
            'v1' => '/api/v1'
        ]
    ]);
});
