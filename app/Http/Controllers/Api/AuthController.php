<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            if ($existingUser->google_id && !$existingUser->password) {
                throw ValidationException::withMessages([
                    'email' => ['This email is associated with a Google account. Please sign in with Google.'],
                ]);
            }
            throw ValidationException::withMessages([
                'email' => ['This email is already registered.'],
            ]);
        }

        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,name', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
        ]);

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->preferences()->create();
        $user->progress()->create();

        $user->load(['preferences', 'progress', 'badges']);

        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send registration email: ' . $e->getMessage());
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully registered',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new \App\Http\Resources\UserProfileResource($user)
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $user = User::with(['preferences', 'progress', 'badges'])->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['invalid credentials.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new \App\Http\Resources\UserProfileResource($user)
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
        ]);

        $user = $request->user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send verification email after update: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Email updated successfully. Verification link sent!',
            // We can return the updated user profile representation if helpful
            'user' => new \App\Http\Resources\UserProfileResource($user)
        ]);
    }
}
