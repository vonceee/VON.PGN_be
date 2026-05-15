<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CollectiblePlayer;
use App\Services\GachaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GachaController extends Controller
{
    protected $gachaService;

    public function __construct(GachaService $gachaService)
    {
        $this->gachaService = $gachaService;
    }

    /**
     * Get all available players in the pool.
     */
    public function index()
    {
        $packsAvailable = 0;
        if (Auth::check()) {
            $user = Auth::user();
            $this->gachaService->ensureDailyPacksReset($user);
            $packsAvailable = $user->daily_packs_available;
        }

        return response()->json([
            'players' => CollectiblePlayer::all(),
            'packs_available' => $packsAvailable,
        ]);
    }

    /**
     * Get the authenticated user's collection.
     */
    public function collection()
    {
        $user = Auth::user();
        $this->gachaService->ensureDailyPacksReset($user);
        $collection = $user->collectibles()->with('collectiblePlayer')->get();

        return response()->json($collection);
    }

    /**
     * Perform a pull.
     */
    public function pull(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|in:1,10',
        ]);

        try {
            $results = $this->gachaService->pull(Auth::user(), $request->count);
            return response()->json([
                'success' => true,
                'results' => $results,
                'remaining_packs' => Auth::user()->daily_packs_available,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
