<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Check if user exists first to avoid leaking user existence
        $user = \App\Models\User::where('email', $request->email)->first();
        
        // Always return success to prevent email enumeration
        // But actually try to send the email if user exists
        if ($user) {
            try {
                $status = Password::broker()->sendResetLink(
                    $request->only('email')
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send password reset email: ' . $e->getMessage());
                // Return generic message - don't reveal the actual error to user
                return response()->json(['message' => 'If an account matches, a reset link was sent.'], 200);
            }

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => __($status)]);
            }

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        // User doesn't exist - still return success to prevent enumeration
        return response()->json(['message' => 'If an account matches, a reset link was sent.']);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
