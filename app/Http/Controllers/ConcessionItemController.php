<?php

namespace App\Http\Controllers;

use App\Models\ConcessionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class ConcessionItemController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $items = ConcessionItem::active()->get();
        return $this->createSuccessResponse($items);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $validatedData = $validator->validated();
        $validatedData['is_active'] = true;
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('concession_items', 'public');
            $validatedData['image'] = $path; // Lưu đường dẫn vào DB
        }


        $item = ConcessionItem::create($validatedData);
        return $this->createSuccessResponse($item, 201);
    }

    public function show($id)
    {
        $item = ConcessionItem::find($id);
        if (!$item || !$item->is_active) {
            return $this->createErrorResponse('Concession item not found', 404);
        }
        return $this->createSuccessResponse($item);
    }

    public function update(Request $request, $id)
    {
        $item = ConcessionItem::find($id);
        if (!$item || !$item->is_active) {
            return $this->createErrorResponse('Concession item not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'item_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $item->update($validator->validated());
        return $this->createSuccessResponse($item);
    }

    public function destroy($id)
    {
        $item = ConcessionItem::find($id);
        if (!$item || !$item->is_active) {
            return $this->createErrorResponse('Concession item not found', 404);
        }

        $item->is_active = false;
        $item->save();

        return $this->createSuccessResponse(['message' => 'Concession item deleted successfully']);
    }
} 