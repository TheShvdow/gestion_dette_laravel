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

// Temporary setup route - DELETE AFTER USE
Route::get('/setup-user', function () {
    try {
        $role = \App\Models\Role::where('libelle', 'Boutiquier')->first();

        if (!$role) {
            return response()->json([
                'error' => 'Role Boutiquier not found. Run seeders first.',
                'roles' => \App\Models\Role::all()->pluck('libelle')
            ], 500);
        }

        $existing = \App\Models\User::where('login', 'boutiquer')->first();

        if ($existing) {
            $existing->password = \Illuminate\Support\Facades\Hash::make('Boutiquier@2024');
            $existing->save();

            return response()->json([
                'message' => 'User password updated',
                'login' => 'boutiquer',
                'password' => 'Boutiquier@2024'
            ]);
        }

        $user = \App\Models\User::create([
            'nom' => 'Boutiquier',
            'prenom' => 'Principal',
            'login' => 'boutiquer',
            'password' => \Illuminate\Support\Facades\Hash::make('Boutiquier@2024'),
            'roleId' => $role->id,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'login' => 'boutiquer',
            'password' => 'Boutiquier@2024',
            'user_id' => $user->id
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});
