<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;

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

Route::prefix('v1')->group(function () {
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store','show']);
});

Route::prefix('v1') -> middleware('auth:api','check.boutiquier')->group(function () {
    Route::apiResource('/articles', ArticleController::class)->only(['store','index','show']);
    Route::post('/articles/libelle', [ArticleController::class, 'findByLibelle']);
    Route::patch('articles/stock/{id}', [ArticleController::class, 'updateStock']);
    Route::post('/articles/stock', [ArticleController::class, 'updateMultipleStocks']);
    Route::delete('/articles/{id}', [ArticleController::class, 'deleteArticle']);
    Route::patch('/articles/{id}/restore', [ArticleController::class, 'restoreArticle']); // Optionnel

});


Route::middleware('auth:api')->group(function () {
    Route::post('/v1/logout', [AuthController::class, 'logout']);
});

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:api')->post('/register', [AuthController::class, 'register']);
    
    
});