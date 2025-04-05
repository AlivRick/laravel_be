<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;

class MovieController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $movies = Movie::all();
        return $this->createSuccessResponse($movies);
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
            'poster_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'genre_ids' => 'required|array',  // Cho phép nhập nhiều genre_id
            'genre_ids.*' => 'exists:genre,genre_id' // Kiểm tra các genre_id có tồn tại không
        ]);
        $validatedData['is_active'] = true;
        // Tạo movie mới, nhưng bỏ genre_ids ra vì nó không thuộc bảng movie
        $movie = Movie::create(collect($validatedData)->except('genre_ids')->toArray());

        if ($request->hasFile('poster_url')) {
            $path = $request->file('poster_url')->store('movies', 'public');
            $validatedData['poster_url'] = $path; // Lưu đường dẫn vào DB
        }

        // Gán thể loại vào bảng moviegenre
        $movie->genres()->attach($validatedData['genre_ids']);

        return $this->createSuccessResponse($movie->load('genres'), 201);
    }

    public function show($id)
    {
        $movie = Movie::findOrFail($id);
        return $this->createSuccessResponse($movie);
    }

    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $movie->update($request->all());
        return $this->createSuccessResponse($movie);
    }

    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            return $this->createErrorResponse('Unauthorized. Admin role required.', 403);
        }
        
        $movie = Movie::where('movie_id', $id)->first();

        if (!$movie || $movie->is_active === false) {
            return $this->createErrorResponse('Movie not found', 404);
        }

        $movie->is_active = false;
        $movie->save();

        return $this->createSuccessResponse(['message' => 'Movie deleted successfully']);
    }

    private function isAdmin()
    {
        $user = Auth::user();
        return $user && $user->role->role_name === "Administrator";
    }
}
