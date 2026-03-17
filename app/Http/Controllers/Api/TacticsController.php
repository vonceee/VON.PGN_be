<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Puzzle;
use Illuminate\Http\Request;

class TacticsController extends Controller
{
    // 1. Give the user a puzzle based on their rating
    public function getDailyPuzzle(Request $request)
    {
        $user = $request->user('sanctum');
        if ($user && !$user->progress) {
            $user->progress()->create();
            $user->refresh();
        }
        $userRating = $user ? ($user->progress->puzzle_rating ?? 1200) : 1200; // Default to 1200 if not logged in

        // Find a puzzle within 150 Elo points of the user's rating
        $puzzle = Puzzle::whereBetween('rating', [$userRating - 150, $userRating + 150])
            ->inRandomOrder()
            ->first();

        // Fallback if we don't have enough puzzles yet
        if (!$puzzle) {
            $puzzle = Puzzle::inRandomOrder()->first();
        }

        return response()->json(['data' => $puzzle]);
    }

    public function solve(Request $request)
    {
        $request->validate([
            'puzzle_id' => 'required|exists:puzzles,id',
            'success' => 'required|boolean',
        ]);

        $user = $request->user('sanctum');
        if (!$user)
            return response()->json(['error' => 'Unauthorized'], 401);

        $puzzle = Puzzle::find($request->puzzle_id);

        $ratingChange = $request->success ? 15 : -10;
        $xpEarned = $request->success ? 110 : 0;

        $progress = $user->progress;
        if (!$progress) {
            $progress = $user->progress()->create();
        }

        /**
         * Update Rating
         */
        $currentRating = $progress->puzzle_rating ?? 1200;
        $newRating = $currentRating + $ratingChange;
        if ($newRating < 400)
            $newRating = 400;
        $progress->puzzle_rating = $newRating;

        /**
         * Update Streak
         */
        $currentStreak = $progress->puzzle_streak ?? 0;
        $newStreak = $request->success ? $currentStreak + 1 : 0;
        $progress->puzzle_streak = $newStreak;

        $progress->save();

        /**
         * Update XP
         */
        $leveledUp = false;
        if ($xpEarned > 0) {
            $leveledUp = $progress->gainExperience($xpEarned);
        }

        return response()->json([
            'success' => true,
            'new_rating' => $progress->puzzle_rating,
            'rating_change' => $ratingChange,
            'new_streak' => $newStreak,
            'xp_earned' => $xpEarned,
            'leveled_up' => $leveledUp,
        ]);
    }
}
