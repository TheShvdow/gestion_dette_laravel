<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\ArticleController;
use App\Http\Controllers\Web\DetteController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\HealthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Health check pour Render.com (pas de middleware, pas de redirection)
Route::get('/health', [HealthController::class, 'check'])->name('health');

// Page d'accueil publique
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Dashboard commun (adapté selon le rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes Admin
    Route::middleware(['check.role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // Routes Boutiquier
    Route::middleware(['check.role:Boutiquier'])->prefix('boutiquier')->name('boutiquier.')->group(function () {
        // Clients
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');

        // Articles
        Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
        Route::post('/articles/{article}/stock', [ArticleController::class, 'updateStock'])->name('articles.stock');

        // Dettes
        Route::get('/dettes', [DetteController::class, 'index'])->name('dettes.index');
        Route::post('/dettes', [DetteController::class, 'store'])->name('dettes.store');
        Route::get('/dettes/{dette}', [DetteController::class, 'show'])->name('dettes.show');
        Route::post('/dettes/{dette}/paiement', [DetteController::class, 'addPaiement'])->name('dettes.paiement');
        Route::delete('/dettes/{dette}', [DetteController::class, 'destroy'])->name('dettes.destroy');
    });

    // Routes Client
    Route::middleware(['check.role:Client'])->prefix('client')->name('client.')->group(function () {
        Route::get('/dettes', function () {
            $client = \App\Models\Client::where('user_id', auth()->id())->first();
            $dettes = $client ? \App\Models\Dette::where('client_id', $client->id)->paginate(15) : [];

            return Inertia::render('Client/Dettes/Index', [
                'dettes' => $dettes,
            ]);
        })->name('dettes.index');
    });
});
