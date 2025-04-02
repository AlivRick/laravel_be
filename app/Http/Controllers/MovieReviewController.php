<?php

namespace App\Http\Controllers;

use App\Models\MovieReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class MovieReviewController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $reviews = MovieReview::with(['movie', 'user'])->get();
        return $this->createSuccessResponse($reviews);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|string|size:24|exists:movie,movie_id',
            'user_id' => 'required|string|size:24|exists:user,user_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $review = MovieReview::create($validator->validated());
        return $this->createSuccessResponse($review->load(['movie', 'user']), 201);
    }

    public function show($id)
    {
        $review = MovieReview::with(['movie', 'user'])->find($id);
        if (!$review) {
            return $this->createErrorResponse('Movie review not found', 404);
        }
        return $this->createSuccessResponse($review);
    }

    public function update(Request $request, $id)
    {
        $review = MovieReview::find($id);
        if (!$review) {
            return $this->createErrorResponse('Movie review not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->createErrorResponse($validator->errors()->first(), 400);
        }

        $review->update($validator->validated());
        return $this->createSuccessResponse($review->load(['movie', 'user']));
    }

    public function destroy($id)
    {
        $review = MovieReview::find($id);
        if (!$review) {
            return $this->createErrorResponse('Movie review not found', 404);
        }

        $review->delete();
        return $this->createSuccessResponse(['message' => 'Movie review deleted successfully']);
    }
} 