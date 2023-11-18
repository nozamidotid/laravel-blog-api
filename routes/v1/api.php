<?php

use App\Http\Controllers\AUth\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\AuthApiMiddleware;
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
    Route::controller(UserController::class)->group(function () {
        Route::get('user',  'getCurrentUser');
        Route::patch('user',  'update');
        Route::delete('user',  'logout');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('category',  'index');
        Route::post('category',  'store');
    });

});
