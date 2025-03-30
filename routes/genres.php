<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GenreController;

Route::prefix('genres')->group(function () {
    Route::get('/', [GenreController::class, 'index']);
    Route::get('/{id}', [GenreController::class, 'show']);
    Route::post('/', [GenreController::class, 'store']);
    Route::put('/{id}', [GenreController::class, 'update']);
    Route::delete('/{id}', [GenreController::class, 'destroy']);
});
