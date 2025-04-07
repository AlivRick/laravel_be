<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'poster_url' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'genre_ids' => 'required|array',
            'genre_ids.*' => 'exists:genre,genre_id'
        ]);

        try {
            // Xử lý upload ảnh
            if ($request->hasFile('poster_url')) {
                $file = $request->file('poster_url');
                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                // Lưu vào thư mục storage/app/public/movies
                $path = $file->storeAs('movies', $filename, 'public');
                $validatedData['poster_url'] = $path;
            }

            $validatedData['is_active'] = true;

            // Tạo movie mới
            $movie = Movie::create(collect($validatedData)->except('genre_ids')->toArray());

            // Gán thể loại
            if (isset($validatedData['genre_ids'])) {
                $movie->genres()->attach($validatedData['genre_ids']);
            }

            return $this->createSuccessResponse($movie->load('genres'), 201);
        } catch (\Exception $e) {
            Log::error('Error creating movie: ' . $e->getMessage());
            return $this->createErrorResponse('Error creating movie: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $movie = Movie::findOrFail($id);
        return $this->createSuccessResponse($movie);
    }

    public function update(Request $request, $id)
    {
        try {
            $movie = Movie::findOrFail($id);

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
                'genre_ids' => 'required|array',
                'genre_ids.*' => 'exists:genre,genre_id'
            ]);

            // Xử lý upload ảnh mới nếu có
            if ($request->hasFile('poster_url')) {
                $file = $request->file('poster_url');
                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                
                // Xóa ảnh cũ nếu tồn tại
                if ($movie->poster_url && Storage::disk('public')->exists($movie->poster_url)) {
                    Storage::disk('public')->delete($movie->poster_url);
                }
                
                // Lưu ảnh mới
                $path = $file->storeAs('movies', $filename, 'public');
                $validatedData['poster_url'] = $path;
            }

            // Cập nhật thông tin phim
            $movie->update(collect($validatedData)->except('genre_ids')->toArray());

            // Cập nhật thể loại
            if (isset($validatedData['genre_ids'])) {
                $movie->genres()->sync($validatedData['genre_ids']);
            }

            return $this->createSuccessResponse($movie->load('genres'));
        } catch (\Exception $e) {
            Log::error('Error updating movie: ' . $e->getMessage());
            return $this->createErrorResponse('Error updating movie: ' . $e->getMessage(), 500);
        }
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
