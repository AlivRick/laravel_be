<?php

namespace App\Http\Controllers;

use App\Models\TheaterRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use App\Models\SeatTemplate;

class TheaterRoomController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $rooms = TheaterRoom::all();
        return $this->createSuccessResponse($rooms);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cinema_complex_id' => 'required|string|size:24',
            'template_id' => 'required|string|size:24',
            'room_name' => 'required|string|max:255',
            'room_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }
        
        $seat_template = SeatTemplate::find($request->template_id);
        if (!$seat_template) {
            return $this->createErrorResponse('Seat template not found', 404);
        }

        $total_seats = $seat_template->total_rows * $seat_template->seats_per_row;
        //set additions to models
        $request->merge(['capacity' => $total_seats]);
        $request->merge(['is_active' => true]);
        $room = TheaterRoom::create($request->all());
        return $this->createSuccessResponse($room, 201);
    }

    public function show($id)
    {
        $room = TheaterRoom::find($id);
        if (!$room) {
            return $this->createErrorResponse('Room not found', 404);
        }
        return $this->createSuccessResponse($room);
    }

    public function update(Request $request, $id)
    {
        $room = TheaterRoom::find($id);
        if (!$room) {
            return $this->createErrorResponse('Room not found', 404);
        }

        $room->update($request->all());
        return $this->createSuccessResponse($room);
    }

    public function destroy($id)
    {
        $room = TheaterRoom::find($id);
        if (!$room) {
            return $this->createErrorResponse('Room not found', 404);
        }

        $room->is_active = false;
        $room->save();
        return $this->createSuccessResponse(['message' => 'Room deleted successfully']);
    }
}
