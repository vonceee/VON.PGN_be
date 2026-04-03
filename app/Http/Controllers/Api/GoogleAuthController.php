<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        // Use stateless() to avoid session requirement for API routes
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error('Google auth failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'exception_class' => get_class($e),
            ]);
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:4200');
            return redirect("{$frontendUrl}/auth/google/callback?error=google_auth_failed");
        }

        $user = User::where('google_id', $googleUser->id)->first();

        if (!$user) {
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // User exists with email/password - link Google account
                if ($user->google_id) {
                    // Already linked to another Google account
                    $frontendUrl = env('FRONTEND_URL', 'http://localhost:4200');
                    return redirect("{$frontendUrl}/auth/google/callback?error=email_already_linked");
                }
                $user->update([
                    'google_id' => $googleUser->id,
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->name ?? $googleUser->email,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'email_verified_at' => now(),
                ]);
            }
        }

        if (!$user->preferences) {
            $user->preferences()->create();
        }
        if (!$user->progress) {
            $user->progress()->create();
        }

        $token = $user->createToken('google-auth')->plainTextToken;

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:4200');

        return redirect("{$frontendUrl}/auth/google/callback?token={$token}");
    }
}
