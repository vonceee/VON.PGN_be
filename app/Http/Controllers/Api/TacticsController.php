<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Puzzle;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        return response()->json([
            'success' => true,
            'new_rating' => $progress->puzzle_rating,
            'rating_change' => $ratingChange,
            'new_streak' => $newStreak,
        ]);
    }

    public function leaderboard(Request $request)
    {
        $limit = 10;
        $cacheKey = 'tactics_leaderboard:' . app()->environment();
        $cacheDuration = 1800; // 30 minutes

        $cachedData = Cache::remember($cacheKey, $cacheDuration, function () use ($limit) {
            // Top 10 by Rating
            $topRating = UserProgress::with('user:id,name,email')
                ->orderBy('puzzle_rating', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($item, $index) {
                    return [
                        'rank' => $index + 1,
                        'user_id' => $item->user_id,
                        'username' => $item->user->name,
                        'score' => $item->puzzle_rating,
                    ];
                });

            // Top 10 by Streak
            $topStreak = UserProgress::with('user:id,name,email')
                ->orderBy('puzzle_streak', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($item, $index) {
                    return [
                        'rank' => $index + 1,
                        'user_id' => $item->user_id,
                        'username' => $item->user->name,
                        'score' => $item->puzzle_streak,
                    ];
                });

            return ['rating' => $topRating, 'streak' => $topStreak];
        });

        $topRating = collect($cachedData['rating']);
        $topStreak = collect($cachedData['streak']);

        $user = $request->user('sanctum');
        $myRatingStats = null;
        $myStreakStats = null;

        if ($user) {
            $userProgress = $user->progress ?? $user->progress()->firstOrCreate([]);
            
            // Calculate Rating Rank
            $ratingRank = UserProgress::where('puzzle_rating', '>', $userProgress->puzzle_rating)->count() + 1;
            $myRatingStats = [
                'rank' => $ratingRank,
                'score' => $userProgress->puzzle_rating,
                'in_top' => $ratingRank <= $limit
            ];

            // Calculate Streak Rank
            $streakRank = UserProgress::where('puzzle_streak', '>', $userProgress->puzzle_streak)->count() + 1;
            $myStreakStats = [
                'rank' => $streakRank,
                'score' => $userProgress->puzzle_streak,
                'in_top' => $streakRank <= $limit
            ];
        }

        return response()->json([
            'rating' => $topRating,
            'streak' => $topStreak,
            'my_stats' => [
                'rating' => $myRatingStats,
                'streak' => $myStreakStats,
            ]
        ]);
    }
}
