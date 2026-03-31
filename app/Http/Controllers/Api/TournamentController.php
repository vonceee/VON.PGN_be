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

        $tournaments = $query->with('creator')
            ->orderByRaw('COALESCE((SELECT verified_organizer FROM users WHERE users.id = tournaments.created_by), 0) DESC')
            ->orderBy('start_date', 'desc')
            ->get();

        return TournamentResource::collection($tournaments);
    }

    public function show(Request $request, $slug)
    {
        $tournament = Tournament::where('slug', $slug)->with('creator')->firstOrFail();

        $tournament->increment('view_count');

        $isBookmarked = false;
        if ($user = $request->user()) {
            $isBookmarked = $tournament->isBookmarkedBy($user);
        }

        return (new TournamentResource($tournament))
            ->additional(['is_bookmarked' => $isBookmarked]);
    }
}
