<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Movie::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'original_title' => 'nullable|string',
            'director' => 'nullable|string',
            'cast' => 'nullable|string',
            'description' => 'nullable|string',
            'duration' => 'required|integer',
            'release_date' => 'required|date',
            'end_date' => 'nullable|date',
            'country' => 'nullable|string',
            'language' => 'nullable|string',
            'age_restriction' => 'nullable|string',
            'trailer_url' => 'nullable|string',
            'poster_url' => 'nullable|string',
            'genre_ids' => 'required|array',  // Cho phép nhập nhiều genre_id
            'genre_ids.*' => 'exists:genre,genre_id' // Kiểm tra các genre_id có tồn tại không
        ]);
        $validatedData['is_active'] = true;
        // Tạo movie mới, nhưng bỏ genre_ids ra vì nó không thuộc bảng movie
        $movie = Movie::create(collect($validatedData)->except('genre_ids')->toArray());

        // Gán thể loại vào bảng moviegenre
        $movie->genres()->attach($validatedData['genre_ids']);

        return response()->json([
            'message' => 'Movie created successfully',
            'data' => $movie->load('genres') // Load genres luôn để kiểm tra
        ], 201);
    }

    public function show($id)
    {
        return Movie::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $movie->update($request->all());
        return response()->json(['message' => 'Movie update successfully', 'data' => $movie], 201);
    }

    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Admin role required.'], 403);
        }
        
        $movie = Movie::where('movie_id', $id)->first();

        if (!$movie || $movie->is_active === false) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $movie->is_active = false;
        $movie->save();

        return response()->json(['message' => 'Movie deleted successfully']);
    }

    private function isAdmin()
    {
        $user = Auth::user();
        return $user && $user->role->role_name === "Administrator";
    }
}
