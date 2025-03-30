<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TheaterRoomController;

Route::prefix('theater-rooms')->group(function () {
    Route::get('/', [TheaterRoomController::class, 'index']);
    Route::post('/', [TheaterRoomController::class, 'store']);
    Route::get('/{id}', [TheaterRoomController::class, 'show']);
    Route::put('/{id}', [TheaterRoomController::class, 'update']);
    Route::delete('/{id}', [TheaterRoomController::class, 'destroy']);
});
