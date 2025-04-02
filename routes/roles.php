<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store'])->middleware('role:Administrator');
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update'])->middleware('role:Administrator');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('role:Administrator');
}); 