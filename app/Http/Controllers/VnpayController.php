<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\TicketType;
use App\Models\ConcessionItem;
use App\Models\ShowtimeSeat;
use App\Models\Booking;
use App\Traits\ApiResponse;
use App\Models\BookingConcession;
use App\Models\BookingDetail;
use App\Models\Showtime;
use App\Models\PaymentHistory;

class VnpayController extends Controller
{
    use ApiResponse;
    public function checkout(Request $request)
    {
        $user = Auth::user(); // chuẩn
        $validator = Validator::make($request->all(), [
            'orderInfo' => 'required|array',
            'orderInfo.booking_details' => 'required|array',
            'orderInfo.booking_details.*.showtime_id' => 'required|string|size:24|exists:showtime,showtime_id',
            'orderInfo.booking_details.*.seat_id' => 'required|string|size:24|exists:seat,seat_id',
            'orderInfo.booking_details.*.ticket_type_id' => 'required|string|size:24|exists:tickettype,ticket_type_id',
            'orderInfo.booking_concessions' => 'nullable|array',
            'orderInfo.booking_concessions.*.item_id' => 'required|string|size:24|exists:concessionitem,item_id',
            'orderInfo.booking_concessions.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }
        
        $validatedData = $validator->validated();
        $orderInfo = $validatedData['orderInfo'];
        $bookingDetails = $orderInfo['booking_details'];
        $bookingConcessions = $orderInfo['booking_concessions'] ?? [];

        $totalAmount = 0;

        foreach ($bookingDetails as $detail) {
            $isBooked = ShowtimeSeat::where('showtime_id', $detail['showtime_id'])
                ->where('seat_id', $detail['seat_id'])
                ->where('is_booked', true)
                ->exists();
        
            if ($isBooked) {
                return $this->createErrorResponse('This seat is no longer available.', 409); // 409 Conflict hợp lý hơn
            }
        }
        // Tính giá cho booking details
        foreach ($bookingDetails as &$detail) {
            $showtime = Showtime::findOrFail($detail['showtime_id']);
            $ticketType = TicketType::findOrFail($detail['ticket_type_id']);
            $price = $showtime->price * (1 - $ticketType->discount_percentage);
            $detail['price'] = $price;
            $totalAmount += $price;
        }

        // Tính giá cho booking concessions
        foreach ($bookingConcessions as &$concession) {
            $item = ConcessionItem::findOrFail($concession['item_id']);
            $price = $item->price * $concession['quantity'];
            $concession['price'] = $price;
            $totalAmount += $price;
        }

        // Lưu thông tin vào bảng Booking
        $booking = Booking::create([
            'user_id' => $user->user_id,
            'payment_method_id' => 'WOAUiPjNyYdexWA65AYJWJTl', // Payment method cố định
            'payment_status' => 'pending', // Trạng thái thanh toán là 'pending'
            'booking_status' => 'pending', // Trạng thái booking là 'pending'
            'booking_time' => now(), // Thời gian đặt vé là thời gian hiện tại
            'total_amount' => $totalAmount,
        ]);

        // Lưu thông tin BookingDetails
        foreach ($bookingDetails as $detail) {
            $detail['booking_id'] = $booking->booking_id;
            BookingDetail::create($detail);
        }

        // Lưu thông tin BookingConcessions
        foreach ($bookingConcessions as $concession) {
            $concession['booking_id'] = $booking->booking_id;
            BookingConcession::create($concession);
        }

        // Lưu lịch sử thanh toán vào PaymentHistory
        PaymentHistory::create([
            'booking_id' => $booking->booking_id,
            'payment_method_id' => 'WOAUiPjNyYdexWA65AYJWJTl', // Cũng là payment_method cố định
            'amount' => $totalAmount,
            'payment_time' => now(),
            'payment_status' => 'pending', // Trạng thái thanh toán là 'pending'
        ]);

        // Các thông tin cần thiết cho VNPAY
        $vnp_TmnCode = '86WO5C45'; // Mã Merchant
        $vnp_HashSecret = 'GE1PM8E7IRB6C9YRYBJ1HQNKPNOOLYTO'; // Secret
        $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        $vnp_Returnurl = url('api/vnpay/return');

        $vnp_Amount = $totalAmount * 100;
        $vnp_TxnRef = time(); // Mã giao dịch random
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => json_encode($orderInfo),  // Sử dụng JSON để giữ thông tin booking
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $booking->booking_id, // Sử dụng booking_id làm mã giao dịch
        ];

        // Sắp xếp dữ liệu theo thứ tự alphabets để hash
        ksort($inputData);
        $query = http_build_query($inputData);
        $hashData = urldecode($query);

        // Hash dữ liệu để tạo SecureHash
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $inputData['vnp_SecureHash'] = $vnp_SecureHash;

        // Tạo URL để chuyển hướng đến VNPAY
        $vnpUrl = $vnp_Url . '?' . http_build_query($inputData);

        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'payment_url' => $vnpUrl
        ]);
    }



    public function return(Request $request)
    {
        $vnp_ResponseCode = $request->input('vnp_ResponseCode'); // 00 là thành công
        $bookingId = $request->input('vnp_TxnRef'); // booking_id

        $booking = Booking::with('bookingDetails')->findOrFail($bookingId);
        $payment_History = PaymentHistory::where('booking_id', $bookingId)->first();
        if ($vnp_ResponseCode == '00') {
            // Update payment_status

            $booking->update([
                'payment_status' => 'completed',
                'booking_status' => 'confirmed',
            ]);
            // Lưu lịch sử thanh toán vào PaymentHistory
            $payment_History ->update([
                'payment_status' => 'completed',
                'payment_time' => now(),
            ]);
           
            // Update is_booked
            foreach ($booking->bookingDetails as $detail) {
                ShowtimeSeat::where('showtime_id', $detail->showtime_id)
                    ->where('seat_id', $detail->seat_id)
                    ->update(['is_booked' => true]);

                // Xoá Redis
                ShowtimeSeat::releaseSeat($detail->showtime_id, $detail->seat_id);
            }
            return redirect('/payment-success');
        }

        // Trường hợp fail
        $booking->update([
            'payment_status' => 'failed',
            'booking_status' => 'cancelled',
        ]);

        return redirect('/payment-failed');
    }
}
