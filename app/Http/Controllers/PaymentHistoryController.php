<?php

namespace App\Http\Controllers;

use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class PaymentHistoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $histories = PaymentHistory::with(['booking', 'paymentMethod'])->get();
        return $this->createSuccessResponse($histories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|string|size:24|exists:booking,booking_id',
            'payment_method_id' => 'required|string|size:24|exists:paymentmethod,payment_method_id',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'nullable|string',
            'payment_time' => 'required|date',
            'payment_status' => 'required|string|in:pending,completed,failed',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $history = PaymentHistory::create($validator->validated());
        return $this->createSuccessResponse($history->load(['booking', 'paymentMethod']), 201);
    }

    public function show($id)
    {
        $history = PaymentHistory::with(['booking', 'paymentMethod'])->find($id);
        if (!$history) {
            return $this->createErrorResponse('Payment history not found', 404);
        }
        return $this->createSuccessResponse($history);
    }

    public function update(Request $request, $id)
    {
        $history = PaymentHistory::find($id);
        if (!$history) {
            return $this->createErrorResponse('Payment history not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'transaction_id' => 'nullable|string',
            'payment_status' => 'sometimes|string|in:pending,completed,failed',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $history->update($validator->validated());
        return $this->createSuccessResponse($history->load(['booking', 'paymentMethod']));
    }

    public function destroy($id)
    {
        $history = PaymentHistory::find($id);
        if (!$history) {
            return $this->createErrorResponse('Payment history not found', 404);
        }

        $history->delete();
        return $this->createSuccessResponse(['message' => 'Payment history deleted successfully']);
    }
} 