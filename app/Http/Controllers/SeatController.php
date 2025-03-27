<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\SeatChangeHistory;
use App\Models\SeatTemplate;
use App\Models\TheaterRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatController extends Controller
{
    public function changeSeatType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|string|size:24',
            'row_letter' => 'required|string|size:1',
            'seat_num' => 'required|string',
            'new_type' => 'required|string|max:20',
            'user_id' => 'required|string|size:24',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $seat = Seat::where('room_id', $request->room_id)
            ->where('seat_row', $request->row_letter)
            ->where('seat_number', $request->seat_num)
            ->first();

        if (!$seat) {
            return response()->json(['message' => 'Seat not found'], 404);
        }

        $currentType = $seat->seat_type;
        $seat->seat_type = $request->new_type;
        $seat->save();

        SeatChangeHistory::create([
            'seat_id' => $seat->seat_id,
            'room_id' => $request->room_id,
            'changed_by' => $request->user_id,
            'previous_state' => $currentType,
            'current_state' => $request->new_type,
            'change_reason' => "Changed seat type from $currentType to {$request->new_type}",
        ]);

        return response()->json(['message' => 'Seat type changed successfully'], 200);
    }

    public function disableSeat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|string|size:24',
            'row_letter' => 'required|string|size:1',
            'seat_num' => 'required|string',
            'reason' => 'required|string',
            'user_id' => 'required|string|size:24',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $seat = Seat::where('room_id', $request->room_id)
            ->where('seat_row', $request->row_letter)
            ->where('seat_number', $request->seat_num)
            ->first();

        if (!$seat) {
            return response()->json(['message' => 'Seat not found'], 404);
        }

        $seat->is_available = false;
        $seat->save();

        SeatChangeHistory::create([
            'seat_id' => $seat->seat_id,
            'room_id' => $request->room_id,
            'changed_by' => $request->user_id,
            'previous_state' => 'Available',
            'current_state' => 'Disabled',
            'change_reason' => $request->reason,
        ]);

        return response()->json(['message' => 'Seat disabled successfully'], 200);
    }

    public function generateSeatsFromTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|string|size:24',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Lấy template_id từ phòng chiếu
        $templateId = TheaterRoom::where('room_id', $request->room_id)->value('template_id');

        if (!$templateId) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        // Lấy thông tin từ template
        $template = SeatTemplate::find($templateId);
        $totalRows = $template->total_rows;
        $seatsPerRow = $template->seats_per_row;

        // Tạo ghế cho từng hàng
        for ($rowCounter = 0; $rowCounter < $totalRows; $rowCounter++) {
            $rowLetter = chr(65 + $rowCounter); // Convert number to letter (0->A, 1->B, ...)

            for ($seatCounter = 1; $seatCounter <= $seatsPerRow; $seatCounter++) {
                Seat::create([
                    'room_id' => $request->room_id,
                    'seat_row' => $rowLetter,
                    'seat_number' => $seatCounter,
                    'seat_type' => 'Standard',
                    'is_available' => true,
                    'is_active' => true,
                ]);
            }
        }

        return response()->json(['message' => 'Seats generated from template successfully'], 200);
    }


    public function mergeSeats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|string|size:24',
            'primary_row' => 'required|string|size:1',
            'primary_seat' => 'required|string',
            'secondary_row' => 'required|string|size:1',
            'secondary_seat' => 'required|string|size:1',
            'user_id' => 'required|string|size:24',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $primarySeat = Seat::where('room_id', $request->room_id)
            ->where('seat_row', $request->primary_row)
            ->where('seat_number', $request->primary_seat)
            ->first();

        $secondarySeat = Seat::where('room_id', $request->room_id)
            ->where('seat_row', $request->secondary_row)
            ->where('seat_number', $request->secondary_seat)
            ->first();

        if (!$primarySeat || !$secondarySeat) {
            return response()->json(['message' => 'One or both seats not found'], 404);
        }

        $primarySeat->seat_type = 'Couple';
        $primarySeat->save();

        $secondarySeat->is_merged = true;
        $secondarySeat->merged_with_seat_id = $primarySeat->seat_id;
        $secondarySeat->is_available = false;
        $secondarySeat->save();

        SeatChangeHistory::create([
            'seat_id' => $secondarySeat->seat_id,
            'room_id' => $request->room_id,
            'changed_by' => $request->user_id,
            'previous_state' => 'Standard',
            'current_state' => 'Merged',
            'change_reason' => "Merged with seat {$request->primary_row}{$request->primary_seat}",
        ]);

        SeatChangeHistory::create([
            'seat_id' => $primarySeat->seat_id,
            'room_id' => $request->room_id,
            'changed_by' => $request->user_id,
            'previous_state' => 'Standard',
            'current_state' => 'Couple',
            'change_reason' => "Primary seat in merge with {$request->secondary_row}{$request->secondary_seat}",
        ]);

        return response()->json(['message' => 'Seats merged successfully'], 200);
    }

    public function resetSeat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|string|size:24',
            'row_letter' => 'required|string|size:1',
            'seat_num' => 'required|string',
            'user_id' => 'required|string|size:24',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $seat = Seat::where('room_id', $request->room_id)
            ->where('seat_row', $request->row_letter)
            ->where('seat_number', $request->seat_num)
            ->first();

        if (!$seat) {
            return response()->json(['message' => 'Seat not found'], 404);
        }

        $currentState = json_encode([
            'type' => $seat->seat_type,
            'merged' => $seat->is_merged,
            'available' => $seat->is_available,
        ]);

        $seat->seat_type = 'Standard';
        $seat->is_merged = false;
        $seat->merged_with_seat_id = null;
        $seat->is_available = true;
        $seat->save();

        SeatChangeHistory::create([
            'seat_id' => $seat->seat_id,
            'room_id' => $request->room_id,
            'changed_by' => $request->user_id,
            'previous_state' => $currentState,
            'current_state' => '{"type":"Standard", "merged":false, "available":true}',
            'change_reason' => 'Reset to default state',
        ]);

        return response()->json(['message' => 'Seat reset successfully'], 200);
    }
}