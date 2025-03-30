<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeatController;

Route::prefix('seats')->group(function () {
    Route::post('change-type', [SeatController::class, 'changeSeatType']);
    Route::post('disable', [SeatController::class, 'disableSeat']);
    Route::post('generate', [SeatController::class, 'generateSeatsFromTemplate']);
    Route::post('merge-seats', [SeatController::class, 'mergeSeats']);
    Route::post('reset-seat', [SeatController::class, 'resetSeat']);
});
