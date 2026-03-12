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

    // 2. Process the result of their attempt
    public function solve(Request $request)
    {
        $request->validate([
            'puzzle_id' => 'required|exists:puzzles,id',
            'success' => 'required|boolean', // Did they solve it correctly?
        ]);

        $user = $request->user('sanctum');
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $puzzle = Puzzle::find($request->puzzle_id);

        // A simplified Elo system:
        // Win = +15 Rating, Lose = -10 Rating.
        // You can upgrade this to a true mathematical Elo formula later!
        $ratingChange = $request->success ? 15 : -10;
        $xpEarned = $request->success ? 110 : 0; // Give 10 XP for getting it right

        // Update the user's progress
        $progress = $user->progress;
        if (!$progress) {
            $progress = $user->progress()->create();
        }

        $currentRating = $progress->puzzle_rating ?? 1200;
        $newRating = $currentRating + $ratingChange;

        // Prevent ratings from dropping below 400 (absolute beginner)
        if ($newRating < 400) $newRating = 400;

        $progress->puzzle_rating = $newRating;

        // Add XP logic if you have an XP column on your user model:
        $currentXp = $progress->experience_points ?? 0;
        $progress->experience_points = $currentXp + $xpEarned;

        // Update puzzle streak — increment on win, reset on fail
        $currentStreak = $progress->puzzle_streak ?? 0;
        $newStreak = $request->success ? $currentStreak + 1 : 0;
        $progress->puzzle_streak = $newStreak;

        $progress->save();

        return response()->json([
            'success' => true,
            'new_rating' => $progress->puzzle_rating,
            'rating_change' => $ratingChange,
            'xp_earned' => $xpEarned,
            'new_streak' => $newStreak,
        ]);
    }
}
