<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::post('/', [BookingController::class, 'store']);
    Route::get('/{id}', [BookingController::class, 'show']);
    Route::put('/{id}', [BookingController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [BookingController::class, 'destroy'])->middleware('role:Administrator');
});
