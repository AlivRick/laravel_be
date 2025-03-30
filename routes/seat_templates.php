<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeatTemplateController;

Route::prefix('seat-templates')->group(function () {
    Route::get('/', [SeatTemplateController::class, 'index']);
    Route::post('/', [SeatTemplateController::class, 'store']);
    Route::get('/{id}', [SeatTemplateController::class, 'show']);
    Route::put('/{id}', [SeatTemplateController::class, 'update']);
    Route::delete('/{id}', [SeatTemplateController::class, 'destroy']);
});
