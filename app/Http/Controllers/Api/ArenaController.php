<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArenaResource;
use App\Models\Arena;
use Illuminate\Http\Request;

class ArenaController extends Controller
{
    public function index(Request $request)
    {
        $query = Arena::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $arenas = $query->with('creator')
            ->orderBy('start_date', 'desc')
            ->get();

        return ArenaResource::collection($arenas);
    }

    public function show(Request $request, $slug)
    {
        $arena = Arena::where('slug', $slug)->with('creator')->firstOrFail();
        return new ArenaResource($arena);
    }

    /**
     * Internal endpoint for the microservice to finalize an arena.
     */
    public function finalizeArenaInternal(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        if ($request->header('X-Internal-Secret') !== config('services.chess.internal_secret')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $arena = Arena::where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        $standings = $request->input('standings', []);
        
        // Find winner (highest score)
        $winnerName = null;
        if (!empty($standings)) {
            $topPlayer = $standings[0]; // Assuming already sorted by microservice
            
            $user = \App\Models\User::find($topPlayer['userId']);
            $winnerName = $user ? $user->name : 'Unknown';
        }

        $arena->update([
            'status' => 'past',
            'standings' => $standings,
            'winner' => $winnerName,
            'current_participants' => count($standings)
        ]);

        return response()->json(['success' => true]);
    }
}
