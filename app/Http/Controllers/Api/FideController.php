<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FideFederation;
use App\Models\FidePlayer;
use Illuminate\Http\Request;

class FideController extends Controller
{
    public function players(Request $request)
    {
        $query = FidePlayer::query()->with('federation');

        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('fed')) {
            $query->where('federation_code', $request->fed);
        }

        if ($request->has('title')) {
            $query->where('title', $request->title);
        }

        // Default to top rated if no search
        if (!$request->has('search')) {
            $query->orderByDesc('rating_standard');
        }

        return response()->json($query->paginate(30));
    }

    public function federations()
    {
        return response()->json(
            FideFederation::orderByDesc('player_count')->get()
        );
    }

    public function ranking(Request $request)
    {
        $type = $request->get('type', 'standard');
        $column = 'rating_' . $type;

        // Verify column exists to prevent injection
        if (!in_array($type, ['standard', 'rapid', 'blitz'])) {
            $column = 'rating_standard';
        }

        return response()->json(
            FidePlayer::with('federation')
                ->where('is_active', true)
                ->whereNotNull($column)
                ->select([
                    'fide_id', 'name', 'federation_code', 'title', 
                    'rating_standard', 'rating_rapid', 'rating_blitz', 
                    'birth_year', 'is_active'
                ])
                ->orderByDesc($column)
                ->limit(100)
                ->get()
        );
    }

    public function show(int $fideId)
    {
        $player = FidePlayer::with('federation')->findOrFail($fideId);
        return response()->json($player);
    }
}
