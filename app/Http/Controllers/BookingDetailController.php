<?php

namespace App\Http\Controllers;

use App\Models\BookingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class BookingDetailController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $details = BookingDetail::with(['booking', 'showtime', 'seat', 'ticketType'])->get();
        return $this->createSuccessResponse($details);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|string|size:24|exists:booking,booking_id',
            'showtime_id' => 'required|string|size:24|exists:showtime,showtime_id',
            'seat_id' => 'required|string|size:24|exists:seat,seat_id',
            'ticket_type_id' => 'required|string|size:24|exists:tickettype,ticket_type_id',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $detail = BookingDetail::create($validator->validated());
        return $this->createSuccessResponse($detail->load(['booking', 'showtime', 'seat', 'ticketType']), 201);
    }

    public function show($id)
    {
        $detail = BookingDetail::with(['booking', 'showtime', 'seat', 'ticketType'])->find($id);
        if (!$detail) {
            return $this->createErrorResponse('Booking detail not found', 404);
        }
        return $this->createSuccessResponse($detail);
    }

    public function update(Request $request, $id)
    {
        $detail = BookingDetail::find($id);
        if (!$detail) {
            return $this->createErrorResponse('Booking detail not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $detail->update($validator->validated());
        return $this->createSuccessResponse($detail->load(['booking', 'showtime', 'seat', 'ticketType']));
    }

    public function destroy($id)
    {
        $detail = BookingDetail::find($id);
        if (!$detail) {
            return $this->createErrorResponse('Booking detail not found', 404);
        }

        $detail->delete();
        return $this->createSuccessResponse(['message' => 'Booking detail deleted successfully']);
    }
} 