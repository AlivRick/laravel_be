<?php

use App\Http\Controllers\ConcessionItemController;
use Illuminate\Support\Facades\Route;

Route::prefix('concession-items')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [ConcessionItemController::class, 'index']);
    Route::post('/', [ConcessionItemController::class, 'store'])->middleware('role:Administrator');
    Route::get('/{id}', [ConcessionItemController::class, 'show']);
    Route::put('/{id}', [ConcessionItemController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [ConcessionItemController::class, 'destroy'])->middleware('role:Administrator');
}); 