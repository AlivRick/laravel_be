<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use App\Models\ShowtimeSeat;
use App\Models\Seat;
use Illuminate\Support\Facades\Log;

class ShowtimeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $showtimes = Showtime::with(['movie', 'theaterroom'])->get();
        return $this->createSuccessResponse($showtimes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|string|size:24|exists:movie,movie_id',
            'room_id' => 'required|string|size:24|exists:theaterroom,room_id',
            'start_time' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        // Lấy thông tin phim để tính end_time
        $movie = Movie::find($request->movie_id);
        if (!$movie || !$movie->is_active) {
            return $this->createErrorResponse('Movie not found or inactive', 404);
        }

        // Tính end_time dựa vào start_time và duration của phim
        $start_time = Carbon::parse($request->start_time);
        $end_time = $start_time->copy()->addMinutes($movie->duration);

        // Kiểm tra xem phòng có trống trong khung giờ này không
        $conflictingShowtime = Showtime::where('room_id', $request->room_id)
            ->where('is_active', true)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->where(function ($q) use ($start_time, $end_time) {
                    // Kiểm tra xem start_time có nằm trong khoảng thời gian của một suất chiếu khác không
                    $q->where('start_time', '<=', $start_time)
                        ->where('end_time', '>', $start_time);
                })->orWhere(function ($q) use ($start_time, $end_time) {
                    // Kiểm tra xem end_time có nằm trong khoảng thời gian của một suất chiếu khác không
                    $q->where('start_time', '<', $end_time)
                        ->where('end_time', '>=', $end_time);
                })->orWhere(function ($q) use ($start_time, $end_time) {
                    // Kiểm tra xem có suất chiếu nào nằm hoàn toàn trong khoảng thời gian này không
                    $q->where('start_time', '>=', $start_time)
                        ->where('end_time', '<=', $end_time);
                });
            })
            ->first();

        if ($conflictingShowtime) {
            return $this->createErrorResponse(
                'Room is already booked during this time period. ' .
                    'Existing showtime: ' . $conflictingShowtime->start_time . ' to ' . $conflictingShowtime->end_time,
                400
            );
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = true;
        $validatedData['end_time'] = $end_time;
        // $validatedData['showtime_id'] = Showtime::generateId(); // Gán ID tự động
        $showtime = Showtime::create($validatedData);
        Log::info('Created Showtime ID: ' . $showtime);  // Log showtime_id để kiểm tra
        // Tự tạo dữ liệu trong showtime_seat
        $seats = Seat::where('room_id', $request->room_id)
            ->where('is_available', true)
            ->get();

        
        $showtimeSeats = [];
        foreach ($seats as $seat) {
            if (!Showtime::find($showtime->showtime_id)) {
                return $this->createErrorResponse('Invalid showtime_id', 400);
            }
        
            if (!Seat::find($seat->seat_id)) {
                return $this->createErrorResponse('Invalid seat_id', 400);
            }
            $showtimeSeats[] = [
                'showtime_seat_id' => ShowtimeSeat::generateId(), // Gán ID tự động
                'showtime_id' => $showtime->showtime_id,
                'seat_id' => $seat->seat_id,
                'is_booked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ShowtimeSeat::insert($showtimeSeats);
        return $this->createSuccessResponse($showtime->load(['movie', 'theaterroom']), 201);
    }

    public function show($id)
    {
        $showtime = Showtime::with(['movie', 'theaterroom'])->find($id);
        if (!$showtime || !$showtime->is_active) {
            return $this->createErrorResponse('Showtime not found', 404);
        }
        return $this->createSuccessResponse($showtime);
    }

    public function update(Request $request, $id)
    {
        $showtime = Showtime::find($id);
        if (!$showtime || !$showtime->is_active) {
            return $this->createErrorResponse('Showtime not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'start_time' => 'sometimes|date|after:now',
            'price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $validatedData = $validator->validated();

        if (isset($validatedData['start_time'])) {
            // Nếu start_time được cập nhật, tính lại end_time
            $start_time = Carbon::parse($validatedData['start_time']);
            $validatedData['end_time'] = $start_time->copy()->addMinutes($showtime->movie->duration);

            // Kiểm tra xung đột thời gian
            $conflictingShowtime = Showtime::where('room_id', $showtime->room_id)
                ->where('is_active', true)
                ->where('showtime_id', '!=', $id)
                ->where(function ($query) use ($start_time, $validatedData) {
                    $query->where(function ($q) use ($start_time, $validatedData) {
                        $q->where('start_time', '<=', $start_time)
                            ->where('end_time', '>', $start_time);
                    })->orWhere(function ($q) use ($start_time, $validatedData) {
                        $q->where('start_time', '<', $validatedData['end_time'])
                            ->where('end_time', '>=', $validatedData['end_time']);
                    })->orWhere(function ($q) use ($start_time, $validatedData) {
                        $q->where('start_time', '>=', $start_time)
                            ->where('end_time', '<=', $validatedData['end_time']);
                    });
                })
                ->first();

            if ($conflictingShowtime) {
                return $this->createErrorResponse(
                    'Room is already booked during this time period. ' .
                        'Existing showtime: ' . $conflictingShowtime->start_time . ' to ' . $conflictingShowtime->end_time,
                    400
                );
            }
        }

        $showtime->update($validatedData);
        return $this->createSuccessResponse($showtime->load(['movie', 'theaterroom']));
    }

    public function destroy($id)
    {
        $showtime = Showtime::find($id);
        if (!$showtime || !$showtime->is_active) {
            return $this->createErrorResponse('Showtime not found', 404);
        }

        $showtime->is_active = false;
        $showtime->save();

        return $this->createSuccessResponse(['message' => 'Showtime deleted successfully']);
    }


    public function getShowtimeSeats($showtimeId)
    {
        $showtime = Showtime::with(['showtimeSeats.seat'])->findOrFail($showtimeId);
        $rows = $showtime->showtimeSeats->groupBy('seat.seat_row')->map(function ($seats) {
        $rowname = $seats->first()->seat->seat_row; // row_id hoặc row_name

        return [
            'rowname' => $rowname,
            'rowseats' => $seats->map(function ($showtimeSeat) {
                $seat = $showtimeSeat->seat;
                $isReserved = ShowtimeSeat::isSeatReserved($showtimeSeat->showtime_id, $seat->seat_id);

                return [
                    'seat_type' => $seat->seat_type,
                    'isseat' => true, // nếu muốn check isseat thì kiểm tra thêm is_active / is_available
                    'isoff' => !$seat->is_active,
                    'name' => $seat->seat_number,
                    'issold' => $showtimeSeat->is_booked || $isReserved,
                    'colindex' => null, // nếu có thì lấy, không thì bỏ
                    'seatindex' => null, // nếu có thì lấy, không thì bỏ
                ];
            })->toArray(),
        ];
    });

    return response()->json(['data' => $rows->values()]);
}

    public function reserveSeats(Request $request, $showtimeId)
{
    $seatIds = $request->input('seat_ids'); // Mảng chuỗi seat_id

    // Validate seat_ids
    if (!is_array($seatIds) || empty($seatIds)) {
        return response()->json(['error' => 'seat_ids must be a non-empty array'], 400);
    }

    $showtime = Showtime::findOrFail($showtimeId);

    foreach ($seatIds as $seatId) {
        $showtimeSeat = ShowtimeSeat::where('showtime_id', $showtime->showtime_id) // Sửa showtime_id thành id
            ->where('seat_id', $seatId) // seat_id là varchar
            ->first();

        if (!$showtimeSeat || $showtimeSeat->is_booked || ShowtimeSeat::isSeatReserved($showtimeId, $seatId)) {
            return response()->json(['error' => "Seat $seatId is unavailable"], 400);
        }

        ShowtimeSeat::reserveSeat($showtimeId, $seatId);
    }

    return response()->json(['message' => 'Seats reserved for 15 minutes']);
}

    public function confirmBooking(Request $request, $showtimeId)
    {
        $seatIds = $request->input('seat_ids');
        $showtime = Showtime::findOrFail($showtimeId);

        foreach ($seatIds as $seatId) {
            $showtimeSeat = ShowtimeSeat::where('showtime_id', $showtimeId)
                ->where('seat_id', $seatId)
                ->first();

            if (!$showtimeSeat || $showtimeSeat->is_booked || !ShowtimeSeat::isSeatReserved($showtimeId, $seatId)) {
                return response()->json(['error' => 'Invalid reservation'], 400);
            }

            $showtimeSeat->update(['is_booked' => true]);
            ShowtimeSeat::releaseSeat($showtimeId, $seatId);
        }

        return response()->json(['message' => 'Booking confirmed']);
    }
}
