<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Arena;
use App\Models\ArenaMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArenaChatController extends Controller
{
    /**
     * Get chat messages for the arena lobby.
     */
    public function index(Request $request, $slug)
    {
        $arena = Arena::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $messages = $arena->messages()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json($messages);
    }

    /**
     * Store a new chat message in the arena lobby.
     */
    public function store(Request $request, $slug)
    {
        $arena = Arena::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = $arena->messages()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return response()->json($message->load('user:id,name'));
    }
}
