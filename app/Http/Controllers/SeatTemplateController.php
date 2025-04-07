<?php

namespace App\Http\Controllers;

use App\Models\SeatTemplate;
use App\Models\TheaterRoom;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class SeatTemplateController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $templates = SeatTemplate::all();
        return $this->createSuccessResponse($templates);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'total_rows' => 'required|integer',
            'seats_per_row' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $template = SeatTemplate::create($request->all());
        return $this->createSuccessResponse($template, 201);
    }

    public function show($id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return $this->createErrorResponse('Template not found', 404);
        }
        return $this->createSuccessResponse($template);
    }

    public function update(Request $request, $id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return $this->createErrorResponse('Template not found', 404);
        }

        $template->update($request->all());
        return $this->createSuccessResponse($template);
    }

    public function destroy($id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return $this->createErrorResponse('Template not found', 404);
        }

        $template->is_active = false;
        $template->save();
        return $this->createSuccessResponse(['message' => 'Template deleted successfully']);
    }

    public function updateTemplate(Request $request, $complexId, $roomId)
    {
        try {
            DB::beginTransaction();

            // Tìm phòng chiếu
            $room = TheaterRoom::where('room_id', $roomId)
                ->where('cinema_complex_id', $complexId)
                ->firstOrFail();

            // Cập nhật hoặc tạo template
            $template = SeatTemplate::updateOrCreate(
                ['template_id' => $room->template_id],
                [
                    'template_name' => $request->template_name,
                    'description' => $request->description,
                    'total_rows' => $request->total_rows,
                    'seats_per_row' => $request->seats_per_row
                ]
            );

            // Cập nhật template_id cho phòng chiếu
            $room->template_id = $template->template_id;
            $room->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSeats(Request $request, $complexId, $roomId)
    {
        try {
            DB::beginTransaction();

            // Tìm phòng chiếu
            $room = TheaterRoom::where('room_id', $roomId)
                ->where('cinema_complex_id', $complexId)
                ->firstOrFail();

            // Xóa tất cả ghế cũ
            Seat::where('room_id', $roomId)->delete();

            // Tạo ghế mới
            foreach ($request->seats as $seatData) {
                $seat = new Seat([
                    'room_id' => $roomId,
                    'seat_row' => $seatData['seat_row'],
                    'seat_number' => $seatData['seat_number'],
                    'seat_type' => $seatData['seat_type'],
                    'is_available' => $seatData['is_available'],
                    'is_active' => $seatData['is_active'],
                    'is_merged' => $seatData['is_merged'] ?? false,
                    'merged_with_seat_id' => $seatData['merged_with_seat_id'] ?? null
                ]);
                $seat->save();
            }

            // Cập nhật merged_with_seat_id cho các ghế đôi
            $seats = Seat::where('room_id', $roomId)->get();
            foreach ($seats as $seat) {
                if ($seat->is_merged) {
                    // Tìm ghế còn lại trong cặp ghế đôi
                    $otherSeat = $seats->first(function ($s) use ($seat) {
                        return $s->seat_row === $seat->seat_row && 
                               abs($s->seat_number - $seat->seat_number) === 1;
                    });
                    
                    if ($otherSeat) {
                        $seat->merged_with_seat_id = $otherSeat->seat_id;
                        $seat->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Seats updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update seats: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSeats(Request $request, $complexId, $roomId)
    {
        try {
            // Tìm phòng chiếu
            $room = TheaterRoom::where('room_id', $roomId)
                ->where('cinema_complex_id', $complexId)
                ->firstOrFail();

            // Lấy thông tin template
            $template = SeatTemplate::find($room->template_id);
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            // Lấy danh sách ghế
            $seats = Seat::where('room_id', $roomId)
                ->orderBy('seat_row')
                ->orderBy('seat_number')
                ->get();

            return response()->json([
                'success' => true,
                'template' => $template,
                'seats' => $seats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get seats: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkRoomDesign(Request $request, $complexId, $roomId)
    {
        try {
            // Tìm phòng chiếu
            $room = TheaterRoom::where('room_id', $roomId)
                ->where('cinema_complex_id', $complexId)
                ->firstOrFail();

            // Kiểm tra xem phòng đã có ghế chưa
            $hasSeats = Seat::where('room_id', $roomId)->exists();

            return response()->json([
                'success' => true,
                'has_design' => $hasSeats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check room design: ' . $e->getMessage()
            ], 500);
        }
    }
}
