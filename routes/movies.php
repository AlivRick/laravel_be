<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

Route::prefix('movies')->middleware('jwt.auth')->group(function () {
    Route::get('/', [MovieController::class, 'index']);
    Route::post('/', [MovieController::class, 'store']);
    Route::get('/{id}', [MovieController::class, 'show']);
    Route::put('/{id}', [MovieController::class, 'update']);
    Route::delete('/{id}', [MovieController::class, 'destroy']);
});
