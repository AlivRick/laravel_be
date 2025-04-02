<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Traits\ApiResponse;
use Exception;

class JwtMiddleware
{
    use ApiResponse;
    /**
     * Xử lý request, kiểm tra JWT token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $request->headers->set('Accept', 'application/json');
            // Try to get token from Authorization header
            $token = $request->bearerToken();
            
            // If no token in header, try to get from cookie
            if (!$token) {
                $token = $request->cookie('token');
            }

            if (!$token) {
                return $this -> createErrorResponse('Token not found', 401);
            }

            $user = JWTAuth::parseToken()->authenticate();
            $request->merge(['user' => $user]);
            
            return $next($request);
        } catch (TokenExpiredException $e) {
            return $this -> createErrorResponse('Token expired', 401);
        } catch (TokenInvalidException $e) {
           return $this -> createErrorResponse('Token invalid', 401);
        } catch (\Exception $e) {
            return $this -> createErrorResponse('Token not found', 401);
        }
    }
}
