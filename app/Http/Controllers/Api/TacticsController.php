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

        $puzzle = Puzzle::findOrFail($request->puzzle_id);
        $progress = $user->progress()->firstOrCreate([]);

        /**
         * Dynamic Elo Rating Calculation
         */
        $uRating = $progress->puzzle_rating ?? 1200;
        $uRD = $progress->puzzle_rating_deviation ?? 350;
        $pRating = $puzzle->rating;

        // 1. Calculate Expected Score (Elo formula)
        // 400 is the standard Elo 'scale' constant. 
        // 0.5 = matched, >0.5 = user is favorite (puzzle is easier), <0.5 = puzzle is harder
        $expectedScore = 1 / (1 + pow(10, ($pRating - $uRating) / 400));
        $actualScore = $request->success ? 1 : 0;

        // 2. Dynamic K-Factor based on User Rating Deviation (RD)
        // Newer users (high RD) gain/lose more points to reach their true skill faster.
        // Scale kFactor from ~50 (at 350 RD) down to ~12 (at 50 RD).
        $kFactor = ($uRD / 350) * 38 + 12;
        
        $ratingChange = (int) round($kFactor * ($actualScore - $expectedScore));

        // 3. Guaranteed minimums to ensure puzzle progression feels rewarding
        if ($request->success && $ratingChange < 2) $ratingChange = 2; // Always at least +2
        if (!$request->success && $ratingChange > -2) $ratingChange = -2; // Always at least -2

        /**
         * Update Rating & Stats
         */
        $progress->puzzle_rating = max(400, $uRating + $ratingChange);
        
        // Slightly decrease deviation (user becomes more 'established') 
        // until it hits a floor of 50.
        $progress->puzzle_rating_deviation = max(50, $uRD - 2);

        // Update Streak
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
            // Top 10 by Tactics Rating
            $topTacticsRating = UserProgress::with('user:id,name,email')
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

            // Top 10 by Tactics Streak
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

            // Top 10 by Bullet Rating
            $topBulletRating = \App\Models\User::orderBy('bullet_rating', 'desc')
                ->limit($limit)
                ->get(['id', 'name', 'bullet_rating'])
                ->map(function ($item, $index) {
                    return [
                        'rank' => $index + 1,
                        'user_id' => $item->id,
                        'username' => $item->name,
                        'score' => $item->bullet_rating,
                    ];
                });

            // Top 10 by Blitz Rating
            $topBlitzRating = \App\Models\User::orderBy('blitz_rating', 'desc')
                ->limit($limit)
                ->get(['id', 'name', 'blitz_rating'])
                ->map(function ($item, $index) {
                    return [
                        'rank' => $index + 1,
                        'user_id' => $item->id,
                        'username' => $item->name,
                        'score' => $item->blitz_rating,
                    ];
                });

            // Top 10 by Rapid Rating
            $topRapidRating = \App\Models\User::orderBy('rapid_rating', 'desc')
                ->limit($limit)
                ->get(['id', 'name', 'rapid_rating'])
                ->map(function ($item, $index) {
                    return [
                        'rank' => $index + 1,
                        'user_id' => $item->id,
                        'username' => $item->name,
                        'score' => $item->rapid_rating,
                    ];
                });

            return [
                'tactics_rating' => $topTacticsRating,
                'streak' => $topStreak,
                'bullet_rating' => $topBulletRating,
                'blitz_rating' => $topBlitzRating,
                'rapid_rating' => $topRapidRating
            ];
        });

        $topTacticsRating = collect($cachedData['tactics_rating']);
        $topStreak = collect($cachedData['streak']);
        $topBulletRating = collect($cachedData['bullet_rating']);
        $topBlitzRating = collect($cachedData['blitz_rating']);
        $topRapidRating = collect($cachedData['rapid_rating']);

        $user = $request->user('sanctum');
        $myTacticsRatingStats = null;
        $myStreakStats = null;
        $myBulletRatingStats = null;
        $myBlitzRatingStats = null;
        $myRapidRatingStats = null;

        if ($user) {
            $userProgress = $user->progress ?? $user->progress()->firstOrCreate([]);

            // Calculate Tactics Rating Rank
            $tacticsRatingRank = UserProgress::where('puzzle_rating', '>', $userProgress->puzzle_rating)->count() + 1;
            $myTacticsRatingStats = [
                'rank' => $tacticsRatingRank,
                'score' => $userProgress->puzzle_rating,
                'in_top' => $tacticsRatingRank <= $limit
            ];

            // Calculate Streak Rank
            $streakRank = UserProgress::where('puzzle_streak', '>', $userProgress->puzzle_streak)->count() + 1;
            $myStreakStats = [
                'rank' => $streakRank,
                'score' => $userProgress->puzzle_streak,
                'in_top' => $streakRank <= $limit
            ];

            // Calculate Bullet Rating Rank
            $bulletRatingRank = \App\Models\User::where('bullet_rating', '>', $user->bullet_rating ?? 1500)->count() + 1;
            $myBulletRatingStats = [
                'rank' => $bulletRatingRank,
                'score' => $user->bullet_rating ?? 1500,
                'in_top' => $bulletRatingRank <= $limit
            ];

            // Calculate Blitz Rating Rank
            $blitzRatingRank = \App\Models\User::where('blitz_rating', '>', $user->blitz_rating ?? 1500)->count() + 1;
            $myBlitzRatingStats = [
                'rank' => $blitzRatingRank,
                'score' => $user->blitz_rating ?? 1500,
                'in_top' => $blitzRatingRank <= $limit
            ];

            // Calculate Rapid Rating Rank
            $rapidRatingRank = \App\Models\User::where('rapid_rating', '>', $user->rapid_rating ?? 1500)->count() + 1;
            $myRapidRatingStats = [
                'rank' => $rapidRatingRank,
                'score' => $user->rapid_rating ?? 1500,
                'in_top' => $rapidRatingRank <= $limit
            ];
        }

        return response()->json([
            'tactics_rating' => $topTacticsRating,
            'streak' => $topStreak,
            'bullet_rating' => $topBulletRating,
            'blitz_rating' => $topBlitzRating,
            'rapid_rating' => $topRapidRating,
            'my_stats' => [
                'tactics_rating' => $myTacticsRatingStats,
                'streak' => $myStreakStats,
                'bullet_rating' => $myBulletRatingStats,
                'blitz_rating' => $myBlitzRatingStats,
                'rapid_rating' => $myRapidRatingStats,
            ]
        ]);
    }
}
