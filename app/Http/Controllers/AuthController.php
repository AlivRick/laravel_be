<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Thêm dòng này để sử dụng DB facade
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        $user = Auth::user();
        return response()->json($user);
    }
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|unique:user',
            'email' => 'required|string|email|unique:user',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'role_id' => 'required|exists:role,role_id',
        ]);

        // Kiểm tra kết nối cơ sở dữ liệu
        if (!DB::connection()->getDatabaseName()) {
            return response()->json(['error' => 'Could not connect to the database.'], 500);
        }

        $user = new User();
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->password = bcrypt($validatedData['password']);
        $user->full_name = $validatedData['full_name'];
        $user->phone_number = $validatedData['phone_number'] ?? null;
        $user->date_of_birth = $validatedData['date_of_birth'] ?? null;
        $user->role_id = $validatedData['role_id'];
        $user->is_active = true;
        $user->save();

        $token = Auth::login($user);
        return $this->respondWithToken($token);
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }
}