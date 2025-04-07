<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\BookingConcession;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use App\Models\Showtime;
use App\Models\TicketType;
use App\Models\ConcessionItem;
use Illuminate\Support\Facades\Auth;
use App\Models\ShowtimeSeat; // nhớ import model này ở trên nhé

class BookingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $bookings = Booking::with(['user', 'paymentmethod', 'bookingdetails', 'bookingconcessions'])->get();
        // Simply retrieve all bookings without any relationships
        // $bookings = Booking::all();

        // Check if there are any bookings
        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found'], 404);
        }
        return $this->createSuccessResponse($bookings);
    }

    public function store(Request $request)
    {
        $user = Auth::user(); // chuẩn
        $validator = Validator::make($request->all(), [
            'booking_time' => 'required|date',
            'payment_method_id' => 'required|string|size:24|exists:paymentmethod,payment_method_id',
            'payment_status' => 'required|string|in:pending,completed,failed',
            'booking_status' => 'required|string|in:pending,confirmed,cancelled',
            'booking_details' => 'required|array',
            'booking_details.*.showtime_id' => 'required|string|size:24|exists:showtime,showtime_id',
            'booking_details.*.seat_id' => 'required|string|size:24|exists:seat,seat_id',
            'booking_details.*.ticket_type_id' => 'required|string|size:24|exists:tickettype,ticket_type_id',
            'booking_concessions' => 'nullable|array',
            'booking_concessions.*.item_id' => 'required|string|size:24|exists:concessionitem,item_id',
            'booking_concessions.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $validatedData = $validator->validated();
        $bookingDetails = $validatedData['booking_details'];
        $bookingConcessions = $validatedData['booking_concessions'] ?? [];

        $totalAmount = 0;

        // Tính giá booking details
        foreach ($bookingDetails as &$detail) {
            $showtime = Showtime::findOrFail($detail['showtime_id']);
            $ticketType = TicketType::findOrFail($detail['ticket_type_id']);

            $price = $showtime->price * $ticketType->discount_percentage;
            $detail['price'] = $price;
            $totalAmount += $price;
        }

        // Tính giá booking concessions
        foreach ($bookingConcessions as &$concession) {
            $item = ConcessionItem::findOrFail($concession['item_id']);
            $price = $item->price * $concession['quantity'];
            $concession['price'] = $price;
            $totalAmount += $price;
        }

        unset($validatedData['booking_details'], $validatedData['booking_concessions']);

        $validatedData['total_amount'] = $totalAmount;
        $validatedData['user_id'] = $user->user_id; // Lấy từ token, không lấy từ request nữa

        $booking = Booking::create($validatedData);

        foreach ($bookingDetails as $detail) {
            $detail['booking_id'] = $booking->booking_id;
            BookingDetail::create($detail);
        }

        foreach ($bookingConcessions as $concession) {
            $concession['booking_id'] = $booking->booking_id;
            BookingConcession::create($concession);

            // Update is_booked = true cho seat đã booking
            ShowtimeSeat::where('showtime_id', $detail['showtime_id'])
                ->where('seat_id', $detail['seat_id'])
                ->update(['is_booked' => true]);
            // Reserve the seat in cache
            ShowtimeSeat::releaseSeat($detail['showtime_id'], $detail['seat_id']);
        }

        PaymentHistory::create([
            'booking_id' => $booking->booking_id,
            'payment_method_id' => $booking->payment_method_id,
            'amount' => $booking->total_amount,
            'payment_time' => now(),
            'payment_status' => $booking->payment_status,
        ]);
        
        return $this->createSuccessResponse(
            $booking->load(['user', 'paymentMethod', 'bookingDetails', 'concessions']),
            201
        );
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
