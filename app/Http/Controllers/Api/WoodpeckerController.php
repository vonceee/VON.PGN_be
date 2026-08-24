<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Puzzle;
use App\Models\WoodpeckerSession;
use App\Models\WoodpeckerCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class WoodpeckerController extends Controller
{
    /**
     * Fetch all Woodpecker sessions for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sessions = WoodpeckerSession::where('user_id', $user->id)
            ->with(['cycles' => function ($query) {
                $query->orderBy('cycle_number', 'asc');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json(['data' => $sessions]);
    }

    /**
     * Create a new Woodpecker session and initialize Cycle 1.
     */
    public function store(Request $request)
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'total_puzzles' => 'required|integer|in:5,10,25,50,100,250,500,1000', // Allowed small counts for testing/demo
            'theme' => 'nullable|string',
            'rating_min' => 'nullable|integer|min:0|max:4000',
            'rating_max' => 'nullable|integer|min:0|max:4000',
        ]);

        $name = $request->name;
        $totalPuzzles = $request->total_puzzles;
        $theme = $request->theme;
        $ratingMin = $request->rating_min;
        $ratingMax = $request->rating_max;

        // 1. O(1) Optimized Random Puzzle Query Builder
        $query = DB::table('puzzles');

        if ($theme && $theme !== 'mix') {
            $query->whereRaw("CONCAT(' ', themes, ' ') LIKE ?", ["% {$theme} %"]);
        }

        if ($ratingMin !== null && $ratingMax !== null) {
            $query->whereBetween('rating', [$ratingMin, $ratingMax]);
        } elseif ($ratingMin !== null) {
            $query->where('rating', '>=', $ratingMin);
        } elseif ($ratingMax !== null) {
            $query->where('rating', '<=', $ratingMax);
        }

        // Shuffled subset pick
        $puzzleIds = $this->getRandomPuzzleIds($query, $totalPuzzles);

        // 2. Robust fallback algorithm in case filters are too narrow:
        // Fallback 1: Expand rating limits by 300 Elo
        if (count($puzzleIds) < $totalPuzzles) {
            $fallbackQuery = DB::table('puzzles');
            if ($theme && $theme !== 'mix') {
                $fallbackQuery->whereRaw("CONCAT(' ', themes, ' ') LIKE ?", ["% {$theme} %"]);
            }
            if ($ratingMin !== null && $ratingMax !== null) {
                $fallbackQuery->whereBetween('rating', [$ratingMin - 150, $ratingMax + 150]);
            }
            $puzzleIds = $this->getRandomPuzzleIds($fallbackQuery, $totalPuzzles);
        }

        // Fallback 2: Remove rating limits completely
        if (count($puzzleIds) < $totalPuzzles) {
            $fallbackQuery = DB::table('puzzles');
            if ($theme && $theme !== 'mix') {
                $fallbackQuery->whereRaw("CONCAT(' ', themes, ' ') LIKE ?", ["% {$theme} %"]);
            }
            $puzzleIds = $this->getRandomPuzzleIds($fallbackQuery, $totalPuzzles);
        }

        // Fallback 3: Return any random puzzles globally
        if (count($puzzleIds) < $totalPuzzles) {
            $puzzleIds = $this->getRandomPuzzleIds(DB::table('puzzles'), $totalPuzzles);
        }

        if (empty($puzzleIds)) {
            return response()->json(['error' => 'No puzzles found in the database.'], 422);
        }

        // Ensure we strictly have the requested count or actual count available
        $actualCount = min(count($puzzleIds), $totalPuzzles);
        $puzzleIds = array_slice($puzzleIds, 0, $actualCount);

        // 3. Create Session
        $session = WoodpeckerSession::create([
            'user_id' => $user->id,
            'name' => $name,
            'puzzle_ids' => $puzzleIds,
            'total_puzzles' => $actualCount,
            'rating_min' => $ratingMin,
            'rating_max' => $ratingMax,
            'theme' => $theme,
            'current_cycle_number' => 1,
            'status' => 'active',
        ]);

        // 4. Initialize Cycle 1
        $cycle = WoodpeckerCycle::create([
            'woodpecker_session_id' => $session->id,
            'cycle_number' => 1,
            'status' => 'in_progress',
            'current_puzzle_index' => 0,
            'start_time' => Carbon::now(),
            'total_solved' => 0,
            'total_correct' => 0,
            'total_time_seconds' => 0,
            'attempts' => [],
        ]);

        $session->load('cycles');

        return response()->json([
            'success' => true,
            'session' => $session,
            'cycle' => $cycle,
        ]);
    }

    /**
     * Show detailed state of a session and load the active puzzle.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $session = WoodpeckerSession::where('user_id', $user->id)
            ->with(['cycles' => function ($query) {
                $query->orderBy('cycle_number', 'asc');
            }])
            ->findOrFail($id);

        $activeCycle = $session->cycles()->where('status', 'in_progress')->first();

        // If no active cycle exists, session is finished
        if (!$activeCycle) {
            return response()->json([
                'session' => $session,
                'current_cycle' => null,
                'current_puzzle' => null,
            ]);
        }

        $puzzleIds = $session->puzzle_ids;
        $currentIndex = $activeCycle->current_puzzle_index;

        $puzzle = null;
        if ($currentIndex < count($puzzleIds)) {
            $puzzleId = $puzzleIds[$currentIndex];
            $puzzle = Puzzle::find($puzzleId);
        }

        return response()->json([
            'session' => $session,
            'current_cycle' => $activeCycle,
            'current_puzzle' => $puzzle,
        ]);
    }

    /**
     * Record a solve attempt for the active cycle's current puzzle.
     */
    public function solve(Request $request, $id)
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'success' => 'required|boolean',
            'time_spent_seconds' => 'required|integer|min:0',
            'moves' => 'nullable|string',
        ]);

        $session = WoodpeckerSession::where('user_id', $user->id)->findOrFail($id);
        
        $activeCycle = $session->cycles()->where('status', 'in_progress')->first();
        if (!$activeCycle) {
            return response()->json(['error' => 'No active cycle found for this session.'], 400);
        }

        $puzzleIds = $session->puzzle_ids;
        $currentIndex = $activeCycle->current_puzzle_index;

        if ($currentIndex >= count($puzzleIds)) {
            return response()->json(['error' => 'All puzzles in this cycle have already been solved.'], 400);
        }

        $currentPuzzleId = $puzzleIds[$currentIndex];

        // 1. Record this attempt
        $attempts = $activeCycle->attempts ?? [];
        $attempts[] = [
            'puzzle_id' => $currentPuzzleId,
            'correct' => $request->success,
            'time_spent' => $request->time_spent_seconds,
            'moves' => $request->moves ?? '',
            'solved_at' => Carbon::now()->toIso8601String(),
        ];

        $newIndex = $currentIndex + 1;
        $newCorrect = $activeCycle->total_correct + ($request->success ? 1 : 0);
        $newTimeSpent = $activeCycle->total_time_seconds + $request->time_spent_seconds;

        $activeCycle->attempts = $attempts;
        $activeCycle->current_puzzle_index = $newIndex;
        $activeCycle->total_solved = $newIndex;
        $activeCycle->total_correct = $newCorrect;
        $activeCycle->total_time_seconds = $newTimeSpent;

        $cycleCompleted = $newIndex >= count($puzzleIds);
        $creditsRewarded = 0;
        $nextCycle = null;

        if ($cycleCompleted) {
            $activeCycle->status = 'completed';
            $activeCycle->end_time = Carbon::now();
            $activeCycle->save();

            if ($activeCycle->cycle_number < 4) {
                // Initialize Next Cycle
                $nextCycleNumber = $activeCycle->cycle_number + 1;
                $nextCycle = WoodpeckerCycle::create([
                    'woodpecker_session_id' => $session->id,
                    'cycle_number' => $nextCycleNumber,
                    'status' => 'in_progress',
                    'current_puzzle_index' => 0,
                    'start_time' => Carbon::now(),
                    'total_solved' => 0,
                    'total_correct' => 0,
                    'total_time_seconds' => 0,
                    'attempts' => [],
                ]);
                $session->current_cycle_number = $nextCycleNumber;
            } else {
                // Session fully completed after 4 cycles
                $session->status = 'completed';
            }
            $session->save();
        } else {
            $activeCycle->save();
        }

        // Touch session updated_at timestamp to mark activity
        $session->touch();

        // Load next puzzle if cycle is still in progress
        $nextPuzzle = null;
        if (!$cycleCompleted && $newIndex < count($puzzleIds)) {
            $nextPuzzleId = $puzzleIds[$newIndex];
            $nextPuzzle = Puzzle::find($nextPuzzleId);
        }

        return response()->json([
            'success' => true,
            'cycle_completed' => $cycleCompleted,
            'credits_rewarded' => $creditsRewarded,
            'session' => $session->load('cycles'),
            'current_cycle' => $cycleCompleted ? $nextCycle : $activeCycle,
            'current_puzzle' => $nextPuzzle,
        ]);
    }

    /**
     * Abandon a session.
     */
    public function abandon(Request $request, $id)
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $session = WoodpeckerSession::where('user_id', $user->id)->findOrFail($id);
        $session->status = 'abandoned';
        $session->save();

        $session->cycles()->where('status', 'in_progress')->update([
            'status' => 'completed',
            'end_time' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session->load('cycles'),
        ]);
    }

    /**
     * Delete a session and all its cycles.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $session = WoodpeckerSession::where('user_id', $user->id)->findOrFail($id);

        DB::transaction(function () use ($session) {
            $session->cycles()->delete();
            $session->delete();
        });

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Optimized O(1) hybrid random puzzle picker.
     * Uses range-based primary key picking for dense datasets,
     * and falls back to plucking matching IDs for sparse/filtered datasets.
     */
    private function getRandomPuzzleIds($query, $limit)
    {
        $minMax = DB::table('puzzles')->selectRaw('MIN(id) as min_id, MAX(id) as max_id')->first();
        if (!$minMax || $minMax->min_id === null) {
            return [];
        }

        $minId = $minMax->min_id;
        $maxId = $minMax->max_id;
        $sampled = [];
        $maxAttempts = $limit * 3;
        $attempts = 0;

        while (count($sampled) < $limit && $attempts < $maxAttempts) {
            $attempts++;
            $randomId = rand($minId, $maxId);
            $id = (clone $query)->where('id', '>=', $randomId)->value('id');
            if ($id && !in_array($id, $sampled)) {
                $sampled[] = $id;
            }
        }

        // Fallback for sparse results: pluck and sample matching IDs
        if (count($sampled) < $limit) {
            $allIds = (clone $query)->pluck('id')->toArray();
            if (!empty($allIds)) {
                $count = count($allIds);
                $keys = array_rand($allIds, min($count, $limit));
                $keys = is_array($keys) ? $keys : [$keys];
                $sampled = [];
                foreach ($keys as $k) {
                    $sampled[] = $allIds[$k];
                }
            }
        }

        return $sampled;
    }
}
