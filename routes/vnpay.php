<?php

use App\Http\Controllers\VnpayController;
use Illuminate\Support\Facades\Route;

Route::prefix('vnpay')->middleware(['jwt.auth'])->group(function () {
    Route::post('/checkout', [VnpayController::class, 'checkout']);
    
}); 