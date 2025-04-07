<?php

namespace App\Http\Controllers;

use App\Models\TheaterRoom;
use App\Models\CinemaComplex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use App\Models\SeatTemplate;

class TheaterRoomController extends Controller
{
    use ApiResponse;

    public function index($complexId = null)
    {
        try {
            if ($complexId) {
                $complex = CinemaComplex::findOrFail($complexId);
                $rooms = $complex->theaterRooms()->get();
            } else {
                $rooms = TheaterRoom::all();
            }

            return $this->createSuccessResponse($rooms->toArray());
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), 500);
        }
    }


    public function show($complexId = null, $roomId = null)
    {
        try {
            if ($complexId && $roomId) {
                $room = TheaterRoom::where('cinema_complex_id', $complexId)
                    ->findOrFail($roomId);
            } else {
                $room = TheaterRoom::findOrFail($complexId);
            }

            return $this->createSuccessResponse($room->toArray());
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), 404);
        }
    }

    // Create a new room
    public function store(Request $request, $complexId = null)
    {
        try {
            $validator = Validator::make($request->all(), [
                'room_name' => 'required|string|max:255',
                'room_type' => 'required|string|in:2D,3D,4D,IMAX',
                'template_id' => 'required|string|exists:seattemplate,template_id'
            ]);

            if ($validator->fails()) {
                return $this->createErrorResponse($validator->errors()->first(), 422);
            }

            $seat_template = SeatTemplate::find($request->template_id);
            if (!$seat_template) {
                return $this->createErrorResponse('Seat template not found', 404);
            }

            $total_seats = $seat_template->total_rows * $seat_template->seats_per_row;

            $data = $request->all();
            $data['capacity'] = $total_seats;
            $data['is_active'] = true;

            if ($complexId) {
                $data['cinema_complex_id'] = $complexId;
            }

            $room = TheaterRoom::create($data);
            return $this->createSuccessResponse($room->toArray(), 201);
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), 500);
        }
    }



    public function update(Request $request, $complexId = null, $roomId = null)
    {
        try {
            $validator = Validator::make($request->all(), [
                'room_name' => 'sometimes|required|string|max:255',
                'room_type' => 'sometimes|required|string|in:2D,3D,4D,IMAX',
                'template_id' => 'sometimes|required|string|exists:seattemplate,template_id',
                'is_active' => 'sometimes|required|boolean'
            ]);

            if ($validator->fails()) {
                return $this->createErrorResponse($validator->errors()->first(), 422);
            }
            if ($complexId && $roomId) {
                $room = TheaterRoom::where('cinema_complex_id', $complexId)
                    ->findOrFail($roomId);
            } else {
                $room = TheaterRoom::findOrFail($complexId);
            }

            $room->update($request->all());
            return $this->createSuccessResponse($room->toArray());
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), 500);
        }
    }
    public function destroy($complexId = null, $roomId = null)
    {
        try {
            if ($complexId && $roomId) {
                $room = TheaterRoom::where('cinema_complex_id', $complexId)
                    ->findOrFail($roomId);
            } else {
                $room = TheaterRoom::findOrFail($complexId);
            }

            $room->is_active = false;
            $room->save();

            return $this->createSuccessResponse(['message' => 'Room deleted successfully']);
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), 500);
        }
    }
}
