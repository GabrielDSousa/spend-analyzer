<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\TransactionController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(TransactionController::class)->prefix('/transactions')->group(function () {
        Route::post('/', 'store');
        Route::get('/all', 'index');
        Route::prefix('/{transaction}')->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'update');
            Route::delete('/', 'destroy');
        });
    });

    Route::controller(MapController::class)->prefix('/maps')->group(function () {
        Route::post('/', 'store');
        Route::get('/all', 'index');
        Route::prefix('/{map}')->group(function () {
            Route::get('/', 'show');
            Route::delete('/', 'destroy');
        });
    });

    Route::controller(CsvController::class)->prefix('/csv')->group(function () {
        Route::post('/', 'load');
    });
});


Route::controller(AuthController::class)->prefix('/auth')->group(function () {
    Route::post('/signup', 'signup');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});
