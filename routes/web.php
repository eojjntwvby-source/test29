<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/documentation');
});

Route::get('/docs', function () {
    return redirect('/api/documentation');
});

// Simple Swagger UI
Route::get('/api/documentation', function () {
    $swaggerJsonUrl = url('/api/docs.json');

    return response()->view('swagger-ui', compact('swaggerJsonUrl'));
});

Route::get('/api/docs.json', function () {
    $filePath = storage_path('api-docs/api-docs.json');
    if (!file_exists($filePath)) {
        abort(404, 'API documentation not generated');
    }
    return response()->file($filePath, [
        'Content-Type' => 'application/json'
    ]);
});
