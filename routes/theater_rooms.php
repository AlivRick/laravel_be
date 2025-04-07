<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TheaterRoomController;

// Routes for managing rooms within a cinema complex
Route::prefix('cinema-complexes/{complexId}/rooms')->middleware(['jwt.auth'])->group(function () {
    // Route cho phép Administrator và Moderator
    Route::middleware(['role:Administrator,Moderator'])->group(function () {
        Route::post('/', [TheaterRoomController::class, 'store']);
        Route::put('/{roomId}', [TheaterRoomController::class, 'update']);
        Route::delete('/{roomId}', [TheaterRoomController::class, 'destroy']);
    });

    // Route cho phép tất cả user đã đăng nhập
    Route::get('/', [TheaterRoomController::class, 'index']);
    Route::get('/{roomId}', [TheaterRoomController::class, 'show']);
});

// General theater room routes

Route::prefix('theater-rooms')->middleware(['jwt.auth'])->group(function () {
    // Route chỉ cho phép Administrator
    Route::middleware(['role:Administrator'])->group(function () {
        Route::post('/', [TheaterRoomController::class, 'store']);
        Route::get('/', [TheaterRoomController::class, 'index']);
        Route::delete('/{id}', [TheaterRoomController::class, 'destroy']);
    });

    // Route cho phép Administrator và Moderator
    Route::middleware(['role:Moderator'])->group(function () {
        Route::put('/{id}', [TheaterRoomController::class, 'update']);
    });

    // Route cho phép tất cả user đã đăng nhập
    // Route::get('/', [TheaterRoomController::class, 'index']);
    Route::get('/{id}', [TheaterRoomController::class, 'show']);
});
