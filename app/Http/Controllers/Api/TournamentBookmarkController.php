<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentBookmarkController extends Controller
{
    public function toggle(Request $request, string $slug)
    {
        $tournament = Tournament::where('slug', $slug)->firstOrFail();
        $user = $request->user();

        if ($tournament->isBookmarkedBy($user)) {
            $tournament->bookmarkedBy()->detach($user->id);
            return response()->json([
                'is_bookmarked' => false,
                'message' => 'Bookmark removed',
            ]);
        }

        $tournament->bookmarkedBy()->attach($user->id);
        return response()->json([
            'is_bookmarked' => true,
            'message' => 'Bookmarked',
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $bookmarkedTournaments = $user->bookmarkedTournaments()
            ->with('creator')
            ->orderBy('tournament_bookmarks.created_at', 'desc')
            ->get();

        return \App\Http\Resources\TournamentResource::collection($bookmarkedTournaments);
    }
}
