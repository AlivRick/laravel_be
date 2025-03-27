<?php

namespace App\Http\Controllers;

use App\Models\TheaterRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TheaterRoomController extends Controller
{
    public function index()
    {
        $rooms = TheaterRoom::all();
        return response()->json($rooms, 200);
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
            return response()->json($validator->errors(), 400);
        }

        $room = TheaterRoom::create($request->all());
        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = TheaterRoom::find($id);
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }
        return response()->json($room, 200);
    }

    public function update(Request $request, $id)
    {
        $room = TheaterRoom::find($id);
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $room->update($request->all());
        return response()->json($room, 200);
    }

    public function destroy($id)
    {
        $room = TheaterRoom::find($id);
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $room->delete();
        return response()->json(['message' => 'Room deleted successfully'], 200);
    }
}
