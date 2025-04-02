<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;

class CheckRole
{
    use ApiResponse;

    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return $this->createErrorResponse('Unauthorized', 401);
        }

        // Nếu không có role được chỉ định, cho phép truy cập
        if (!$role) {
            return $next($request);
        }

        $userRole = $user->role->role_name;

        // Kiểm tra quyền truy cập dựa trên role
        switch ($role) {
            case 'Administrator':
                if ($userRole !== 'Administrator') {
                    return $this->createErrorResponse('Access denied. Administrator role required.', 403);
                }
                break;
            case 'Moderator':
                if (!in_array($userRole, ['Administrator', 'Moderator'])) {
                    return $this->createErrorResponse('Access denied. Moderator role required.', 403);
                }
                break;
            case 'User':
                if (!in_array($userRole, ['Administrator', 'Moderator', 'User'])) {
                    return $this->createErrorResponse('Access denied. User role required.', 403);
                }
                break;
        }

        return $next($request);
    }
} 