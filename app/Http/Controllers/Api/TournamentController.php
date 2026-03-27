<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tournaments = $query->orderBy('start_date', 'desc')->get();

        return TournamentResource::collection($tournaments);
    }

    public function show($slug)
    {
        $tournament = Tournament::where('slug', $slug)->firstOrFail();

        return new TournamentResource($tournament);
    }
}
