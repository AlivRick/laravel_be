<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;

class GenreController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $genres = Genre::active()->get();
        return $this->createSuccessResponse($genres);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'genre_name' => 'required|string|max:50|unique:genre,genre_name',
            'description' => 'required|string'
        ]);

        $validatedData['is_active'] = true;
        $genre = Genre::create($validatedData);

        return $this->createSuccessResponse($genre, 201);
    }

    public function show($id)
    {
        $genre = Genre::with('movies')->find($id);
        
        if (!$genre || $genre->is_active) {
            return $this->createErrorResponse('Genre not found', 404);
        }

        return $this->createSuccessResponse($genre);
    }

    public function update(Request $request, $id)
    {
        $genre = Genre::find($id);
        
        if (!$genre || $genre->is_active) {
            return $this->createErrorResponse('Genre not found', 404);
        }

        $validatedData = $request->validate([
            'genre_name' => 'sometimes|string|max:50|unique:genre,genre_name,' . $id . ',genre_id',
            'description' => 'sometimes|string'
        ]);

        $genre->update($validatedData);

        return $this->createSuccessResponse($genre);
    }

    public function destroy($id)
    {
        $genre = Genre::where('genre_id', $id)->first();
        
        if (!$genre || $genre->is_active === false) {
            return $this->createErrorResponse('Genre not found', 404);
        }

        $genre->is_active = false;
        $genre->save();

        return $this->createSuccessResponse(['message' => 'Genre deleted successfully']);
    }
}