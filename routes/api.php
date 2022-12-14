<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// AUTH

Route::group([
    'middleware' => 'jwt.auth'
], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/changepassword', [AuthController::class, 'changePassword']);
});

// PROFILE
Route::group([
    'middleware' => 'jwt.auth'
], function() {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::get('/profiles', [UserController::class, 'getAllUsers'])->middleware('isAdmin');
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::delete('/profile', [UserController::class, 'deleteMyProfile']);
});
Route::get('/profile/{id}', [UserController::class, 'getUserById']);


// GAMES
Route::group(['middleware' => ['jwt.auth', 'isAdmin']
], function() {
    Route::post('/game', [GamesController::class, 'addGame']);
    Route::delete('/game/{id}', [GamesController::class, 'deleteGameById']);
});
Route::get('/games',[GamesController::class, 'getAllGames']);
Route::get('/game/{id}', [GamesController::class, 'getGameById']);
Route::get('/game/name/{name}', [GamesController::class, 'getGameByName']);
