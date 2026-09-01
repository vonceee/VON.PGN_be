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

    /**
     * Update the authenticated user's bughouse win/loss/draw record.
     */
    public function updateRecord(Request $request)
    {
        $request->validate([
            'game_id' => 'required|string',
            'outcome' => 'required|string|in:win,draw,loss',
        ]);

        $user = $request->user();
        $gameId = $request->game_id;
        $outcome = $request->outcome;

        // Perform an atomic check & lock using Cache::add (returns false if the key already exists)
        $cacheKey = "bughouse_processed_game:{$user->id}:{$gameId}";
        if (!\Illuminate\Support\Facades\Cache::add($cacheKey, true, now()->addDay())) {
            return response()->json([
                'message' => 'Game outcome already processed.',
                'bughouse_stats' => [
                    'wins' => (int) $user->bughouse_wins,
                    'draws' => (int) $user->bughouse_draws,
                    'losses' => (int) $user->bughouse_losses,
                ],
            ]);
        }

        if ($outcome === 'win') {
            $user->increment('bughouse_wins');
        } elseif ($outcome === 'draw') {
            $user->increment('bughouse_draws');
        } elseif ($outcome === 'loss') {
            $user->increment('bughouse_losses');
        }

        return response()->json([
            'message' => 'Record updated successfully.',
            'bughouse_stats' => [
                'wins' => (int) $user->bughouse_wins,
                'draws' => (int) $user->bughouse_draws,
                'losses' => (int) $user->bughouse_losses,
            ],
        ]);
    }

    /**
     * Reset the authenticated user's bughouse stats.
     */
    public function resetRecord(Request $request)
    {
        $user = $request->user();
        $user->update([
            'bughouse_wins' => 0,
            'bughouse_draws' => 0,
            'bughouse_losses' => 0,
        ]);

        return response()->json([
            'message' => 'Record reset successfully.',
            'bughouse_stats' => [
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
            ],
        ]);
    }
}
