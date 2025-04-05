<?php

namespace App\Http\Controllers;

use App\Models\CinemaComplex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class CinemaComplexController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $complexes = CinemaComplex::active()->get();
        return $this->createSuccessResponse($complexes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'complex_name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = true;
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cinemas', 'public');
            $validatedData['image'] = $path; // Lưu đường dẫn vào DB
        }
        
        $complex = CinemaComplex::create($validatedData);
        return $this->createSuccessResponse($complex, 201);
    }

    public function show($id)
    {
        $complex = CinemaComplex::with('theaterRooms')->find($id);
        if (!$complex || !$complex->is_active) {
            return $this->createErrorResponse('Cinema complex not found', 404);
        }
        return $this->createSuccessResponse($complex);
    }

    public function update(Request $request, $id)
    {
        $complex = CinemaComplex::find($id);
        if (!$complex || !$complex->is_active) {
            return $this->createErrorResponse('Cinema complex not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'complex_name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'province' => 'sometimes|string|max:100',
            'phone_number' => 'sometimes|string|max:20',
            'email' => 'nullable|email|max:255',
            'opening_time' => 'sometimes|date_format:H:i',
            'closing_time' => 'sometimes|date_format:H:i|after:opening_time',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $complex->update($validator->validated());
        return $this->createSuccessResponse($complex);
    }

    public function destroy($id)
    {
        $complex = CinemaComplex::find($id);
        if (!$complex || !$complex->is_active) {
            return $this->createErrorResponse('Cinema complex not found', 404);
        }

        $complex->is_active = false;
        $complex->save();

        return $this->createSuccessResponse(['message' => 'Cinema complex deleted successfully']);
    }
} 