<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class PaymentMethodController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $methods = PaymentMethod::active()->get();
        return $this->createSuccessResponse($methods);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = true;
        
        $method = PaymentMethod::create($validatedData);
        return $this->createSuccessResponse($method, 201);
    }

    public function show($id)
    {
        $method = PaymentMethod::find($id);
        if (!$method || !$method->is_active) {
            return $this->createErrorResponse('Payment method not found', 404);
        }
        return $this->createSuccessResponse($method);
    }

    public function update(Request $request, $id)
    {
        $method = PaymentMethod::find($id);
        if (!$method || !$method->is_active) {
            return $this->createErrorResponse('Payment method not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'method_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $method->update($validator->validated());
        return $this->createSuccessResponse($method);
    }

    public function destroy($id)
    {
        $method = PaymentMethod::find($id);
        if (!$method || !$method->is_active) {
            return $this->createErrorResponse('Payment method not found', 404);
        }

        $method->is_active = false;
        $method->save();

        return $this->createSuccessResponse(['message' => 'Payment method deleted successfully']);
    }
} 