<?php

namespace App\Http\Controllers;

use App\Models\SeatTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatTemplateController extends Controller
{
    public function index()
    {
        $templates = SeatTemplate::all();
        return response()->json($templates, 200);
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
            return response()->json($validator->errors(), 400);
        }

        $template = SeatTemplate::create($request->all());
        return response()->json($template, 201);
    }

    public function show($id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }
        return response()->json($template, 200);
    }

    public function update(Request $request, $id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $template->update($request->all());
        return response()->json($template, 200);
    }

    public function destroy($id)
    {
        $template = SeatTemplate::find($id);
        if (!$template) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $template->delete();
        return response()->json(['message' => 'Template deleted successfully'], 200);
    }
}
