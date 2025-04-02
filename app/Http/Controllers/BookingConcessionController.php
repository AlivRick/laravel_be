<?php

namespace App\Http\Controllers;

use App\Models\BookingConcession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class BookingConcessionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $concessions = BookingConcession::with(['booking', 'item'])->get();
        return $this->createSuccessResponse($concessions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|string|size:24|exists:booking,booking_id',
            'item_id' => 'required|string|size:24|exists:concessionitem,item_id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $concession = BookingConcession::create($validator->validated());
        return $this->createSuccessResponse($concession->load(['booking', 'item']), 201);
    }

    public function show($id)
    {
        $concession = BookingConcession::with(['booking', 'item'])->find($id);
        if (!$concession) {
            return $this->createErrorResponse('Booking concession not found', 404);
        }
        return $this->createSuccessResponse($concession);
    }

    public function update(Request $request, $id)
    {
        $concession = BookingConcession::find($id);
        if (!$concession) {
            return $this->createErrorResponse('Booking concession not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $concession->update($validator->validated());
        return $this->createSuccessResponse($concession->load(['booking', 'item']));
    }

    public function destroy($id)
    {
        $concession = BookingConcession::find($id);
        if (!$concession) {
            return $this->createErrorResponse('Booking concession not found', 404);
        }

        $concession->delete();
        return $this->createSuccessResponse(['message' => 'Booking concession deleted successfully']);
    }
} 