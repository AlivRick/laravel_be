<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\SeatController;

Route::group(['middleware' => 'api'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
    Route::prefix('genres')->group(function () {
        Route::get('', [GenreController::class, 'index']);
        Route::get('{id}', [GenreController::class, 'show']);
        Route::post('', [GenreController::class, 'store']);
        Route::put('{id}', [GenreController::class, 'update']);
        Route::delete('{id}', [GenreController::class, 'destroy']);
    });
    Route::prefix('seats')->group(function () {
        Route::post('change-type', [SeatController::class, 'changeSeatType']);
        Route::post('disable', [SeatController::class, 'disableSeat']);
        Route::post('generate', [SeatController::class, 'generateSeats']);
    });
});
