<?php

use App\Http\Controllers\TicketTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('ticket-types')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [TicketTypeController::class, 'index']);
    Route::post('/', [TicketTypeController::class, 'store'])->middleware('role:Administrator');
    Route::get('/{id}', [TicketTypeController::class, 'show']);
    Route::put('/{id}', [TicketTypeController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [TicketTypeController::class, 'destroy'])->middleware('role:Administrator');
}); 