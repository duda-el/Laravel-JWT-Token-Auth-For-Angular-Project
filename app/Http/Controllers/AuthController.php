<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        // Validation handled by UserRegisterRequest
        $validatedData = $request->validated();

        // Create the user
        $user = User::create([
            'name' => $validatedData['name'], 
            'email' => $validatedData['email'], 
            'password' => bcrypt($validatedData['password']),
            'role' => $validatedData['role'] ?? 'user', // Default role is 'user'
        ]);

        // Generate token
        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        // Direct validation for login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            // Custom messages for login
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'The password field is required.',
        ]);

        $credentials = $request->only(['email', 'password']);

        // Attempt login
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return $this->respondWithToken($token, auth('api')->user()); // Include user details
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token, $user = null)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user ?? auth('api')->user(), // Include user details
        ]);
    }
}
