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
        $existingEmail = User::where('email', $request->email)->first();

        if ($existingEmail) {
            \Illuminate\Support\Facades\Log::warning('Registration failed: Email already exists', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'email' => ['This email is already registered.'],
            ]);
        }

        $existingUsername = User::where('name', $request->username)->first();

        if ($existingUsername) {
            \Illuminate\Support\Facades\Log::warning('Registration failed: Username already taken', ['username' => $request->username]);
            throw ValidationException::withMessages([
                'username' => ['This username is already taken.'],
            ]);
        }

        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_-]+$/'],
                'email' => 'required|string|email|max:255',
                'password' => [
                    'required',
                    'confirmed',
                    \Illuminate\Validation\Rules\Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                ],
            ]);
        } catch (ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning('Registration validation failed', $e->errors());
            throw $e;
        }

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

        \Illuminate\Support\Facades\Log::info('User registered successfully', ['user_id' => $user->id, 'email' => $user->email]);

        return response()->json([
            'message' => 'User successfully registered',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new \App\Http\Resources\UserProfileResource($user)
        ], 201);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning('Login validation failed', $e->errors());
            throw $e;
        }
        
        $user = User::with(['preferences', 'progress', 'badges'])->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            \Illuminate\Support\Facades\Log::warning('Login failed: Invalid credentials', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        \Illuminate\Support\Facades\Log::info('User logged in successfully', ['user_id' => $user->id]);

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
            'user' => new \App\Http\Resources\UserProfileResource($user)
        ]);
    }
}
