<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Create a success response
     */
    protected function createSuccessResponse($data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ], $status);
    }

    /**
     * Create an error response
     */
    protected function createErrorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }

    /**
     * Create a cookie response
     */
    protected function createCookieResponse($response, string $key, ?string $value, int $expires): JsonResponse
    {
        if ($value === null) {
            return $response;
        }
        return $response->cookie($key, $value, $expires, null, null, true, true);
    }
} 