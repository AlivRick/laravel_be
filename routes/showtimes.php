<?php

use App\Http\Controllers\ShowtimeController;
use Illuminate\Support\Facades\Route;

Route::prefix('showtimes')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [ShowtimeController::class, 'index']);
    Route::post('/', [ShowtimeController::class, 'store'])->middleware('role:Administrator');
    Route::get('/{id}', [ShowtimeController::class, 'show']);
    Route::put('/{id}', [ShowtimeController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [ShowtimeController::class, 'destroy'])->middleware('role:Administrator');
}); 