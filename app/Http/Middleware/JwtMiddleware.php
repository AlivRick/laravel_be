<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;

class JwtMiddleware
{
    /**
     * Xử lý request, kiểm tra JWT token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Kiểm tra token và lấy user
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Authorization failed'], 401);
        }

        // Nếu hợp lệ, tiếp tục request
        return $next($request);
    }
}
