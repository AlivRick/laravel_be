<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class TicketTypeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $types = TicketType::active()->get();
        return $this->createSuccessResponse($types);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = true;
        
        $type = TicketType::create($validatedData);
        return $this->createSuccessResponse($type, 201);
    }

    public function show($id)
    {
        $type = TicketType::find($id);
        if (!$type || !$type->is_active) {
            return $this->createErrorResponse('Ticket type not found', 404);
        }
        return $this->createSuccessResponse($type);
    }

    public function update(Request $request, $id)
    {
        $type = TicketType::find($id);
        if (!$type || !$type->is_active) {
            return $this->createErrorResponse('Ticket type not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'type_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'sometimes|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $type->update($validator->validated());
        return $this->createSuccessResponse($type);
    }

    public function destroy($id)
    {
        $type = TicketType::find($id);
        if (!$type || !$type->is_active) {
            return $this->createErrorResponse('Ticket type not found', 404);
        }

        $type->is_active = false;
        $type->save();

        return $this->createSuccessResponse(['message' => 'Ticket type deleted successfully']);
    }
} 