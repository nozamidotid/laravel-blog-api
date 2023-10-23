<?php

use App\Http\Controllers\AUth\UserController;
use App\Http\Middleware\AuthApiMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware([AuthApiMiddleware::class])->group(function () {
    Route::get('user', [UserController::class, 'getCurrentUser']);
    Route::patch('user', [UserController::class, 'update']);
});
