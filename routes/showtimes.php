<?php

use App\Http\Controllers\ShowtimeController;
use Illuminate\Support\Facades\Route;

Route::prefix('showtimes')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [ShowtimeController::class, 'index']);
    Route::post('/', [ShowtimeController::class, 'store'])->middleware('role:Administrator');
    Route::get('/{id}', [ShowtimeController::class, 'show']);
    Route::put('/{id}', [ShowtimeController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [ShowtimeController::class, 'destroy'])->middleware('role:Administrator');

    // Thêm route cho showtime
    Route::get('/{showtimeId}/seats', [ShowtimeController::class, 'getShowtimeSeats']);
    Route::post('/{showtimeId}/reserve', [ShowtimeController::class, 'reserveSeats']);
    Route::post('/{showtimeId}/confirm', [ShowtimeController::class, 'confirmBooking']);
}); 