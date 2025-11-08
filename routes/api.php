<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:passport')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware(['auth:api', 'check.role:Boutiquier'])->group(function () {
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store','show']);
    Route::post('/clients/{id}/dettes', [ClientController::class, 'getClientDettes']);
    Route::post('/clients/{id}/user', [ClientController::class, 'getClientWithUser']);
});


//Route pour les articles
Route::prefix('v1') -> middleware('auth:api','check.role:Boutiquier')->group(function () {
    Route::apiResource('/articles', ArticleController::class)->only(['store','index','show']);
    Route::post('/articles/libelle', [ArticleController::class, 'findByLibelle']);
    Route::patch('/articles/{id}', [ArticleController::class, 'updateStock']);
    Route::post('/articles/all', [ArticleController::class, 'updateMultipleStocks']);
    Route::delete('/articles/{id}', [ArticleController::class, 'deleteArticle']);
    Route::patch('/articles/{id}/restore', [ArticleController::class, 'restoreArticle']); // Optionnel

});



//Route pour l'authentification
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware(['auth:api', 'check.role:Boutiquier'])->post('/register', [AuthController::class, 'register']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

});
//Route pour les dettes (protégées par authentification)
Route::prefix('v1')->middleware('auth:api')->group(function () {
    Route::get('dettes', [DetteController::class, 'index']);
    Route::middleware('check.role:Boutiquier')->post('dettes', [DetteController::class, 'store']);
    Route::get('dettes/{id}', [DetteController::class, 'show']);
    Route::post('dettes/{id}/articles', [DetteController::class, 'getArticles']);
    Route::get('dettes/{id}/paiements', [DetteController::class, 'listPaiements']);
    Route::middleware('check.role:Boutiquier')->post('dettes/{id}/paiement', [DetteController::class, 'addPaiement']);
});

//Route pour les utilisateurs (Admin uniquement)
Route::prefix('v1')->middleware(['auth:api', 'check.role:Admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
});