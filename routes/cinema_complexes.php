<?php

use App\Http\Controllers\CinemaComplexController;
use Illuminate\Support\Facades\Route;

Route::prefix('cinema-complexes')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [CinemaComplexController::class, 'index']);
    Route::post('/', [CinemaComplexController::class, 'store'])->middleware('role:Administrator');
    Route::get('/{id}', [CinemaComplexController::class, 'show']);
    Route::put('/{id}', [CinemaComplexController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [CinemaComplexController::class, 'destroy'])->middleware('role:Administrator');
}); 