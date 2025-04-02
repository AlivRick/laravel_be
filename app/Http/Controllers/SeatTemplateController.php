<?php

namespace App\Http\Controllers;

use App\Models\SeatTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class SeatTemplateController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $templates = SeatTemplate::all();
        return $this->createSuccessResponse($templates);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'total_rows' => 'required|integer',
            'seats_per_row' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $template = SeatTemplate::create($request->all());
        return $this->createSuccessResponse($template, 201);
    }

    public function show($id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return $this->createErrorResponse('Template not found', 404);
        }
        return $this->createSuccessResponse($template);
    }

    public function update(Request $request, $id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return $this->createErrorResponse('Template not found', 404);
        }

        $template->update($request->all());
        return $this->createSuccessResponse($template);
    }

    public function destroy($id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return $this->createErrorResponse('Template not found', 404);
        }

        $template->delete();
        return $this->createSuccessResponse(['message' => 'Template deleted successfully']);
    }
}
