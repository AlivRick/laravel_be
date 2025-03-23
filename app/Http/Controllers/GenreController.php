<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class GenreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $genres = Genre::active()->get();
        return response()->json(['data' => $genres]);
    }

    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $validatedData = $request->validate([
            'genre_name' => 'required|string|max:50|unique:genre,genre_name',
            'description' => 'required|string'
        ]);

        $validatedData['is_deleted'] = false;
        $genre = Genre::create($validatedData);

        return response()->json(['message' => 'Genre created successfully', 'data' => $genre], 201);
    }

    public function show($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $genre = Genre::with('movies')->find($id);
        
        if (!$genre || $genre->is_deleted) {
            return response()->json(['message' => 'Genre not found'], 404);
        }

        return response()->json(['data' => $genre]);
    }

    public function update(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $genre = Genre::find($id);
        
        if (!$genre || $genre->is_deleted) {
            return response()->json(['message' => 'Genre not found'], 404);
        }

        $validatedData = $request->validate([
            'genre_name' => 'sometimes|string|max:50|unique:genre,genre_name,' . $id . ',genre_id',
            'description' => 'sometimes|string'
        ]);

        $genre->update($validatedData);

        return response()->json(['message' => 'Genre updated successfully', 'data' => $genre]);
    }

    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }

        $genre = Genre::find($id);
        
        if (!$genre || $genre->is_deleted) {
            return response()->json(['message' => 'Genre not found'], 404);
        }

        $genre->is_deleted = true;
        $genre->save();

        return response()->json(['message' => 'Genre deleted successfully']);
    }

    private function isAdmin()
    {
        $user = Auth::user();
        return $user && $user->role_id === 1; // Assuming role_id = 1 is Admin
    }
}