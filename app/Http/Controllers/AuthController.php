<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return $this->createErrorResponse('Invalid credentials', 401);
        }

        // Create cookie response
        $response = $this->createSuccessResponse([
            'user' => Auth::user(),
            'token' => $token
        ]);
        
        return $this->createCookieResponse(
            $response,
            'token',
            $token,
            60 // 1 hour
        );
    }

    public function me()
    {
        return $this->createSuccessResponse(Auth::user());
    }

    public function logout()
    {
        Auth::logout();
        return $this->createSuccessResponse(['message' => 'Successfully logged out']);
    }
    
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'username' => 'required|string|unique:user',
                'email' => 'required|string|email|unique:user',
                'password' => 'required|string|min:8',
                'full_name' => 'required|string',
                'phone_number' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
            ]);

            // Kiểm tra kết nối cơ sở dữ liệu
            if (!DB::connection()->getDatabaseName()) {
                return $this->createErrorResponse('Could not connect to the database.', 500);
            }
            $role = Role::where('name', 'User')->first();

            $user = new User();
            $user->username = $validatedData['username'];
            $user->email = $validatedData['email'];
            $user->password = bcrypt($validatedData['password']);
            $user->full_name = $validatedData['full_name'];
            $user->phone_number = $validatedData['phone_number'] ?? null;
            $user->date_of_birth = $validatedData['date_of_birth'] ?? null;
            $user->role_id = $role->id;
            $user->is_active = true;
            $user->save();

            $token = Auth::login($user);
            
            // Create cookie response
            $response = $this->createSuccessResponse([
                'user' => $user,
                'token' => $token
            ]);
            
            return $this->createCookieResponse(
                $response,
                'token',
                $token,
                60 // 1 hour
            );
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), 500);
        }
    }

    public function refresh()
    {
        try {
            $token = Auth::refresh();
            
            if (!$token) {
                return $this->createErrorResponse('Could not refresh token', 401);
            }
            
            // Create cookie response
            $response = $this->createSuccessResponse([
                'user' => Auth::user(),
                'token' => $token
            ]);
            
            return $this->createCookieResponse(
                $response,
                'token',
                $token,
                60 // 1 hour
            );
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), 401);
        }
    }
}