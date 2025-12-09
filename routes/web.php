<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - API ONLY MODE
|--------------------------------------------------------------------------
|
| This application is now API-only. All routes are in routes/api.php
| The frontend is a separate Vue.js SPA application.
|
*/

// Redirect root to API documentation or health check
Route::get('/', function () {
    return response()->json([
        'message' => 'Gestion Dette API',
        'version' => '1.0.0',
        'endpoints' => [
            'health' => url('/api/health'),
            'api' => url('/api/v1'),
            'docs' => url('/api/documentation'), // Si vous ajoutez Swagger/L5-Swagger plus tard
        ],
    ]);
});
