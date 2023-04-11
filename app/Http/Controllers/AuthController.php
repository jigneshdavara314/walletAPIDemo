<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate input
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], $e->status);
        }

        // Create user
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => bin2hex(openssl_random_pseudo_bytes(30)), // Generate a random API token
        ]);
        $user->save();

        // Return success message
        return response()->json(['message' => 'User registered successfully'], 201);
    }


    public function login(Request $request)
    {
        // Validate input
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], $e->status);
        }

        // Find user
        $user = User::where('email', $request->email)->first();

        // Verify password and generate token if valid
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'access_token' => $user->api_token,
                'token_type' => 'Bearer',
            ]);
        }

        // Return an error if credentials are invalid
        return response()->json([
            'errors' => [
                'email' => ['The provided credentials are incorrect.'],
            ],
        ], 422);
    }
}
