<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\BughouseInviteNotification;

class BughouseController extends Controller
{
    /**
     * Dispatch a lobby invitation to another user.
     */
    public function sendInvite(Request $request)
    {
        $request->validate([
            'receiver_username' => 'required|string|exists:users,name',
        ]);

        $receiver = User::where('name', $request->receiver_username)->firstOrFail();

        if ($receiver->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot invite yourself.',
            ], 422);
        }
        
        // Delete existing unread bughouse invites from this sender to prevent stacking
        $receiver->unreadNotifications()
            ->where('type', BughouseInviteNotification::class)
            ->get()
            ->each(function ($notification) use ($request) {
                if (($notification->data['sender_username'] ?? null) === $request->user()->name) {
                    $notification->delete();
                }
            });

        // Dispatch database notification
        $receiver->notify(new BughouseInviteNotification($request->user()));

        return response()->json([
            'message' => 'Invitation sent successfully.',
        ]);
    }

    /**
     * Cancel an active lobby invitation to another user.
     */
    public function cancelInvite(Request $request)
    {
        $request->validate([
            'receiver_username' => 'required|string|exists:users,name',
        ]);

        $receiver = User::where('name', $request->receiver_username)->firstOrFail();

        $receiver->unreadNotifications()
            ->where('type', BughouseInviteNotification::class)
            ->get()
            ->each(function ($notification) use ($request) {
                if (($notification->data['sender_username'] ?? null) === $request->user()->name) {
                    $notification->delete();
                }
            });

        return response()->json([
            'message' => 'Invitation cancelled successfully.',
        ]);
    }
}
