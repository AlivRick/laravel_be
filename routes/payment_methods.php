<?php

use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment-methods')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [PaymentMethodController::class, 'index']);
    Route::post('/', [PaymentMethodController::class, 'store'])->middleware('role:Administrator');
    Route::get('/{id}', [PaymentMethodController::class, 'show']);
    Route::put('/{id}', [PaymentMethodController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [PaymentMethodController::class, 'destroy'])->middleware('role:Administrator');
}); 