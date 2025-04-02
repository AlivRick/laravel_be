<?php

namespace App\Http\Controllers;

use App\Models\TheaterRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

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
            'capacity' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

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

        $room->delete();
        return $this->createSuccessResponse(['message' => 'Room deleted successfully']);
    }
}
