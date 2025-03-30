<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api'], function () {
    require __DIR__ . '/auth.php';
    require __DIR__ . '/genres.php';
    require __DIR__ . '/seats.php';
    require __DIR__ . '/seat_templates.php';
    require __DIR__ . '/theater_rooms.php';
    require __DIR__ . '/movies.php';
});

// Route::group(['middleware' => 'api'], function () {
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('logout', [AuthController::class, 'logout']);
//     Route::get('me', [AuthController::class, 'me']);
//     Route::post('register', [AuthController::class, 'register']);
//     Route::prefix('genres')->group(function () {
//         Route::get('/', [GenreController::class, 'index']);
//         Route::get('/{id}', [GenreController::class, 'show']);
//         Route::post('/', [GenreController::class, 'store']);
//         Route::put('/{id}', [GenreController::class, 'update']);
//         Route::delete('/{id}', [GenreController::class, 'destroy']);
//     });
//     Route::prefix('seats')->group(function () {
//         Route::post('change-type', [SeatController::class, 'changeSeatType']);
//         Route::post('disable', [SeatController::class, 'disableSeat']);
//         Route::post('generate', [SeatController::class, 'generateSeatsFromTemplate']);
//         Route::post('mergeSeats', [SeatController::class, 'mergeSeats']);
//         Route::post('resetSeat', [SeatController::class, 'resetSeat']); 
//     });
//     // Seat template management routes
//     Route::prefix('seat-templates')->group(function () {
//         Route::get('/', [SeatTemplateController::class, 'index']);
//         Route::post('/', [SeatTemplateController::class, 'store']);
//         Route::get('/{id}', [SeatTemplateController::class, 'show']);
//         Route::put('/{id}', [SeatTemplateController::class, 'update']);
//         Route::delete('/{id}', [SeatTemplateController::class, 'destroy']);
//     });

//     // Theater room management routes
//     Route::prefix('theater-rooms')->group(function () {
//         Route::get('/', [TheaterRoomController::class, 'index']);
//         Route::post('/', [TheaterRoomController::class, 'store']);
//         Route::get('/{id}', [TheaterRoomController::class, 'show']);
//         Route::put('/{id}', [TheaterRoomController::class, 'update']);
//         Route::delete('/{id}', [TheaterRoomController::class, 'destroy']);
//     });

//     Route::prefix('movies')->group(function () {
//         Route::get('/', [MovieController::class, 'index']);
//         Route::post('/', [MovieController::class, 'store']);
//         Route::get('/{id}', [MovieController::class, 'show']);
//         Route::put('/{id}', [MovieController::class, 'update']);
//         Route::delete('/{id}', [MovieController::class, 'destroy']);
//     });
// });
