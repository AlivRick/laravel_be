<?php

use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Route;

Route::prefix('promotions')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [PromotionController::class, 'index']);
    Route::post('/', [PromotionController::class, 'store'])->middleware('role:Administrator');
    Route::get('/{id}', [PromotionController::class, 'show']);
    Route::put('/{id}', [PromotionController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [PromotionController::class, 'destroy'])->middleware('role:Administrator');
}); 