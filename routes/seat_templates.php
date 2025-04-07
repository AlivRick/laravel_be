<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeatTemplateController;

Route::prefix('seat-templates')->middleware(['jwt.auth'])->group(function () {
    // Route cho phép Administrator và Moderator
    Route::middleware(['role:Administrator,Moderator'])->group(function () {
        Route::post('/', [SeatTemplateController::class, 'store']);
        Route::put('/{id}', [SeatTemplateController::class, 'update']);
        Route::delete('/{id}', [SeatTemplateController::class, 'destroy']);
    });

    // Route cho phép tất cả user đã đăng nhập
    Route::get('/', [SeatTemplateController::class, 'index']);
    Route::get('/{id}', [SeatTemplateController::class, 'show']);
});
