<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TournamentResource;
use App\Models\Tournament;
use App\Models\User;
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
            ->orderByRaw('CASE WHEN (SELECT verified_organizer FROM users WHERE users.id = tournaments.created_by) = TRUE THEN 1 ELSE 0 END DESC')
            ->orderBy('start_date', 'desc')
            ->get();

        return TournamentResource::collection($tournaments);
    }

    public function show(Request $request, $slug)
    {
        $tournament = Tournament::where('slug', $slug)->with('creator')->firstOrFail();

        $tournament->increment('view_count');

        $isBookmarked = false;
        if ($user = auth('sanctum')->user()) {
            $isBookmarked = $tournament->isBookmarkedBy($user);
        }

        return (new TournamentResource($tournament))
            ->additional(['is_bookmarked' => $isBookmarked]);
    }

    public function userTournaments(string $id)
    {
        if (is_numeric($id)) {
            $user = User::find($id);
        } else {
            $user = User::where('name', $id)->first();
        }

        if (!$user) {
            return response()->json(['data' => []]);
        }

        $tournaments = Tournament::where('created_by', $user->id)
            ->with('creator')
            ->orderBy('start_date', 'desc')
            ->get();

        return TournamentResource::collection($tournaments);
    }
}
