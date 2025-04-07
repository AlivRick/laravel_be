<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SeatTemplateController;
Route::group(['middleware' => ['force.json', 'api']], function () {
    require __DIR__ . '/auth.php';
    require __DIR__ . '/genres.php';
    require __DIR__ . '/seats.php';
    require __DIR__ . '/bookings.php';
    require __DIR__ . '/seat_templates.php';
    require __DIR__ . '/theater_rooms.php';
    require __DIR__ . '/movies.php';
    require __DIR__ . '/cinema_complexes.php';
    require __DIR__ . '/payment_methods.php';
    require __DIR__ . '/ticket_types.php';
    require __DIR__ . '/showtimes.php';
    require __DIR__ . '/concession_items.php';
    require __DIR__ . '/promotions.php';
    require __DIR__ . '/user_promotions.php';
    require __DIR__ . '/roles.php';
    require __DIR__ . '/vnpay.php';
    Route::get('/vnpay/return', [VnpayController::class, 'return']);
    Route::post('/check-in', [BookingController::class, 'checkIn']);

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
Route::prefix('cinema-complexes/{complexId}/rooms/{roomId}')->group(function () {
    Route::get('/check-design', [SeatTemplateController::class, 'checkRoomDesign']);
    Route::put('/template', [SeatTemplateController::class, 'updateTemplate']);
    Route::put('/seats', [SeatTemplateController::class, 'updateSeats']);
    Route::get('/seats', [SeatTemplateController::class, 'getSeats']);
});
