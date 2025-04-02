<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\BookingConcession;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class BookingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $bookings = Booking::with(['user', 'paymentMethod', 'bookingDetails', 'bookingConcessions'])->get();
        return $this->createSuccessResponse($bookings);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|size:24|exists:user,user_id',
            'booking_time' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'payment_method_id' => 'required|string|size:24|exists:paymentmethod,payment_method_id',
            'payment_status' => 'required|string|in:pending,completed,failed',
            'booking_status' => 'required|string|in:pending,confirmed,cancelled',
            'booking_details' => 'required|array',
            'booking_details.*.showtime_id' => 'required|string|size:24|exists:showtime,showtime_id',
            'booking_details.*.seat_id' => 'required|string|size:24|exists:seat,seat_id',
            'booking_details.*.ticket_type_id' => 'required|string|size:24|exists:tickettype,ticket_type_id',
            'booking_details.*.price' => 'required|numeric|min:0',
            'booking_concessions' => 'nullable|array',
            'booking_concessions.*.item_id' => 'required|string|size:24|exists:concessionitem,item_id',
            'booking_concessions.*.quantity' => 'required|integer|min:1',
            'booking_concessions.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $validatedData = $validator->validated();
        $bookingDetails = $validatedData['booking_details'];
        $bookingConcessions = $validatedData['booking_concessions'] ?? [];
        unset($validatedData['booking_details'], $validatedData['booking_concessions']);

        $booking = Booking::create($validatedData);

        // Create booking details
        foreach ($bookingDetails as $detail) {
            $detail['booking_id'] = $booking->booking_id;
            BookingDetail::create($detail);
        }

        // Create booking concessions
        foreach ($bookingConcessions as $concession) {
            $concession['booking_id'] = $booking->booking_id;
            BookingConcession::create($concession);
        }

        // Create initial payment history
        PaymentHistory::create([
            'booking_id' => $booking->booking_id,
            'payment_method_id' => $booking->payment_method_id,
            'amount' => $booking->total_amount,
            'payment_time' => now(),
            'payment_status' => $booking->payment_status,
        ]);

        return $this->createSuccessResponse($booking->load(['user', 'paymentMethod', 'bookingDetails', 'bookingConcessions']), 201);
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'paymentMethod', 'bookingDetails', 'bookingConcessions'])->find($id);
        if (!$booking) {
            return $this->createErrorResponse('Booking not found', 404);
        }
        return $this->createSuccessResponse($booking);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return $this->createErrorResponse('Booking not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'payment_status' => 'sometimes|string|in:pending,completed,failed',
            'booking_status' => 'sometimes|string|in:pending,confirmed,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $booking->update($validator->validated());

        // Update payment history if payment status changed
        if ($request->has('payment_status')) {
            PaymentHistory::create([
                'booking_id' => $booking->booking_id,
                'payment_method_id' => $booking->payment_method_id,
                'amount' => $booking->total_amount,
                'payment_time' => now(),
                'payment_status' => $booking->payment_status,
            ]);
        }

        return $this->createSuccessResponse($booking->load(['user', 'paymentMethod', 'bookingDetails', 'bookingConcessions']));
    }

    public function destroy($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return $this->createErrorResponse('Booking not found', 404);
        }

        // Soft delete related records
        $booking->bookingDetails()->delete();
        $booking->bookingConcessions()->delete();
        $booking->paymentHistories()->delete();
        $booking->delete();

        return $this->createSuccessResponse(['message' => 'Booking deleted successfully']);
    }
} 