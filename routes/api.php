<?php

use App\Http\Controllers\Api\Backoffice\BoAuthController;
// Player
use App\Http\Controllers\Api\Backoffice\BoPromotionController;
use App\Http\Controllers\Api\Backoffice\BoUserController;
// Backoffice Agent
use App\Http\Controllers\Api\Player\PlayerAuthController;
use App\Http\Controllers\Api\Player\PlayerController;
use App\Http\Controllers\Api\Player\PlayerPromotionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// PLAYER Routes


Route::post('/register', [PlayerAuthController::class, 'register']);
Route::post('/login', [PlayerAuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [PlayerController::class, 'viewProfile']);
    Route::get('/balance', [PlayerController::class, 'viewBalance']);

    Route::get('/promotions', [PlayerPromotionController::class, 'promotions']);
    Route::post('/promotions/claim', [PlayerPromotionController::class, 'claimPromotion']);
});


// BACKOFFICE Routes
Route::prefix('bo')->group(function () {

    Route::post('/login', [BoAuthController::class, 'login']);

    Route::middleware('auth:bo-api')->group(function () {
        Route::get('/me', [BoAuthController::class, 'viewProfile']);
        Route::get('/users', [BoUserController::class, 'getUsers']);
        Route::get('/players/{player}/transactions', [BoUserController::class, 'getTransactions']);

        Route::get('/promotions', [BoPromotionController::class, 'getPromotions']);
        Route::post('/promotions', [BoPromotionController::class, 'createPromotion']);
        Route::delete('/promotions/{id}', [BoPromotionController::class, 'deletePromotion']);
        Route::patch('/promotions/{id}/status', [BoPromotionController::class,'changeStatus']);  //Patch for the partially update a resource
    });
});
