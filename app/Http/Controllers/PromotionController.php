<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class PromotionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $promotions = Promotion::active()->get();
        return $this->createSuccessResponse($promotions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promotion_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'promotion_code' => 'required|string|max:50|unique:promotion,promotion_code',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = true;
        
        $promotion = Promotion::create($validatedData);
        return $this->createSuccessResponse($promotion, 201);
    }

    public function show($id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion || !$promotion->is_active) {
            return $this->createErrorResponse('Promotion not found', 404);
        }
        return $this->createSuccessResponse($promotion);
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion || !$promotion->is_active) {
            return $this->createErrorResponse('Promotion not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'promotion_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'promotion_code' => 'sometimes|string|max:50|unique:promotion,promotion_code,' . $id . ',promotion_id',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $promotion->update($validator->validated());
        return $this->createSuccessResponse($promotion);
    }

    public function destroy($id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion || !$promotion->is_active) {
            return $this->createErrorResponse('Promotion not found', 404);
        }

        $promotion->is_active = false;
        $promotion->save();

        return $this->createSuccessResponse(['message' => 'Promotion deleted successfully']);
    }
} 