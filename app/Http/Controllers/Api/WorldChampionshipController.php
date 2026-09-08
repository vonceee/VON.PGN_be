<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorldChampionshipMatch;
use App\Models\WorldChampionshipGame;
use App\Models\WorldChampionshipGameSection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * Controller for World Chess Championship matchups, games, and editorial stories.
 * 
 * WHY: Powers dedicated championship matchup pages, interactive story viewing,
 *      and the in-place editorial story builder.
 */
class WorldChampionshipController extends Controller
{
    /**
     * List all World Championship matches.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WorldChampionshipMatch::query()
            ->with(['games.sections' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('year', 'desc');

        if ($request->has('era') && $request->era !== 'All') {
            $query->where('era', $request->era);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(champion) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(challenger) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(location) LIKE ?', ["%{$search}%"])
                  ->orWhere('year', 'LIKE', "%{$search}%");
            });
        }

        $matches = $query->get();

        return response()->json([
            'success' => true,
            'data' => $matches,
            'count' => $matches->count(),
        ]);
    }

    /**
     * Show a specific championship matchup with its games and narrative sections.
     */
    public function show(string $slugOrId): JsonResponse
    {
        $match = WorldChampionshipMatch::query()
            ->where('slug', $slugOrId)
            ->orWhere('id', is_numeric($slugOrId) ? (int)$slugOrId : 0)
            ->with(['games.sections' => function ($q) {
                $q->orderBy('order');
            }])
            ->first();

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'World Championship matchup not found.',
            ], 404);
        }

        // Increment view count
        $match->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $match,
        ]);
    }

    /**
     * Create or update a World Championship match along with its game and editorial sections.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'nullable|integer',
            'champion' => 'nullable|string|max:150',
            'challenger' => 'nullable|string|max:150',
            'winner' => 'nullable|string|max:150',
            'score' => 'nullable|string|max:50',
            'format' => 'nullable|string|max:150',
            'location' => 'nullable|string|max:255',
            'era' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'key_highlights' => 'nullable|array',
            'study_id' => 'nullable|integer',
            'slug' => 'nullable|string|max:100',

            // Game payloads
            'game' => 'nullable|array',
            'games' => 'nullable|array',
        ]);

        $title = trim($validated['title']);
        $year = $validated['year'] ?? $this->extractYearFromTitle($title);
        $champion = $validated['champion'] ?? 'Defender';
        $challenger = $validated['challenger'] ?? 'Challenger';
        $winner = $validated['winner'] ?? $champion;
        $score = $validated['score'] ?? '0 - 0';
        $format = $validated['format'] ?? null;
        $location = $validated['location'] ?? null;
        $era = $validated['era'] ?? 'Modern Era (2006-Present)';
        $description = $validated['description'] ?? "World Chess Championship Match duel.";

        // Slug handling with upsert support
        $providedSlug = $validated['slug'] ?? null;
        $match = null;
        if ($providedSlug && $providedSlug !== 'new') {
            $match = WorldChampionshipMatch::where('slug', $providedSlug)->first();
        }

        DB::beginTransaction();
        try {
            if (!$match) {
                $baseSlug = $providedSlug && $providedSlug !== 'new'
                    ? Str::slug($providedSlug)
                    : Str::slug($title);
                
                if (empty($baseSlug)) {
                    $baseSlug = "wcc-{$year}";
                }

                $slug = $baseSlug;
                $counter = 1;
                while (WorldChampionshipMatch::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $match = WorldChampionshipMatch::create([
                    'slug' => $slug,
                    'year' => $year,
                    'display_year' => $validated['display_year'] ?? null,
                    'title' => $title,
                    'champion' => $champion,
                    'challenger' => $challenger,
                    'winner' => $winner,
                    'score' => $score,
                    'format' => $format,
                    'location' => $location,
                    'era' => $era,
                    'description' => $description,
                    'key_highlights' => $validated['key_highlights'] ?? [],
                    'study_id' => $validated['study_id'] ?? null,
                    'games_count' => isset($validated['game']) ? 1 : (isset($validated['games']) ? count($validated['games']) : 0),
                ]);
            } else {
                $match->update([
                    'year' => $year,
                    'display_year' => $validated['display_year'] ?? $match->display_year,
                    'title' => $title,
                    'champion' => $champion,
                    'challenger' => $challenger,
                    'winner' => $winner,
                    'score' => $score,
                    'format' => $format ?? $match->format,
                    'location' => $location ?? $match->location,
                    'era' => $era,
                    'description' => $description,
                    'key_highlights' => $validated['key_highlights'] ?? $match->key_highlights,
                    'study_id' => $validated['study_id'] ?? $match->study_id,
                ]);
            }

            // Sync single game if provided
            if (isset($validated['game'])) {
                $this->syncGame($match, $validated['game']);
            }

            // Sync multiple games if provided
            if (isset($validated['games']) && is_array($validated['games'])) {
                foreach ($validated['games'] as $gData) {
                    $this->syncGame($match, $gData);
                }
            }

            $match->update(['games_count' => $match->games()->count()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'World Championship matchup & editorial story saved successfully.',
                'data' => $match->load('games.sections'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save matchup story: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing matchup and its editorial games.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $match = WorldChampionshipMatch::where('id', is_numeric($id) ? (int)$id : 0)
            ->orWhere('slug', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|nullable|string|max:255',
            'year' => 'sometimes|nullable|integer',
            'champion' => 'sometimes|nullable|string|max:150',
            'challenger' => 'sometimes|nullable|string|max:150',
            'winner' => 'sometimes|nullable|string|max:150',
            'score' => 'sometimes|nullable|string|max:50',
            'format' => 'sometimes|nullable|string|max:150',
            'location' => 'sometimes|nullable|string|max:255',
            'era' => 'sometimes|nullable|string|max:100',
            'description' => 'sometimes|nullable|string',
            'game' => 'nullable|array',
            'games' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $match->update(array_filter($request->only([
                'title', 'year', 'champion', 'challenger', 'winner',
                'score', 'format', 'location', 'era', 'description',
                'key_highlights', 'study_id'
            ]), fn($val) => $val !== null));

            if ($request->has('game')) {
                $this->syncGame($match, $request->input('game'));
            }

            if ($request->has('games') && is_array($request->input('games'))) {
                foreach ($request->input('games') as $gData) {
                    $this->syncGame($match, $gData);
                }
            }

            $match->update(['games_count' => $match->games()->count()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'World Championship story updated successfully.',
                'data' => $match->fresh(['games.sections']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update matchup story: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a World Championship match and all associated games/sections.
     */
    public function destroy(string $id): JsonResponse
    {
        $match = WorldChampionshipMatch::where('id', is_numeric($id) ? (int)$id : 0)
            ->orWhere('slug', $id)
            ->first();

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'World Championship matchup not found.',
            ], 404);
        }

        $match->delete();

        return response()->json([
            'success' => true,
            'message' => 'World Championship matchup deleted successfully.',
        ]);
    }

    /**
     * Sync or create an individual game and its editorial sections.
     */
    private function syncGame(WorldChampionshipMatch $match, array $gameData): WorldChampionshipGame
    {
        $gameNumber = (int)($gameData['game_number'] ?? 1);
        $pgn = $gameData['pgn'] ?? '';

        // Auto-extract White, Black, Result from PGN headers if omitted
        $white = $gameData['white_player']
            ?? $gameData['white']
            ?? $this->extractPgnHeader($pgn, 'White')
            ?? ($gameNumber % 2 === 1 ? $match->champion : $match->challenger);

        $black = $gameData['black_player']
            ?? $gameData['black']
            ?? $this->extractPgnHeader($pgn, 'Black')
            ?? ($gameNumber % 2 === 1 ? $match->challenger : $match->champion);

        $result = $gameData['result']
            ?? $this->extractPgnHeader($pgn, 'Result')
            ?? '*';

        $title = $gameData['title'] ?? "Game {$gameNumber}: {$white} vs. {$black}";
        $slug = $gameData['slug'] ?? Str::slug($title) . "-{$match->id}-g{$gameNumber}";

        $game = $match->games()->where('game_number', $gameNumber)->first();

        $payload = [
            'match_id' => $match->id,
            'game_number' => $gameNumber,
            'slug' => $slug,
            'title' => $title,
            'subtitle' => $gameData['subtitle'] ?? "{$white} vs. {$black}",
            'white_player' => $white,
            'black_player' => $black,
            'result' => $result,
            'game_date' => $gameData['game_date'] ?? $gameData['date'] ?? null,
            'eco' => $gameData['eco'] ?? 'C00',
            'opening_name' => $gameData['opening_name'] ?? $gameData['opening'] ?? 'Classical Opening',
            'pgn' => $pgn,
            'initial_fen' => $gameData['initial_fen'] ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
            'total_plies' => (int)($gameData['total_plies'] ?? 0),
            'narrative_overview' => $gameData['narrative_overview'] ?? null,
            'is_highlighted' => $gameData['is_highlighted'] ?? ($gameNumber === 1),
            'order' => $gameNumber,
        ];

        if ($game) {
            $game->update($payload);
        } else {
            $game = $match->games()->create($payload);
        }

        // Sync editorial narrative sections
        if (isset($gameData['sections']) && is_array($gameData['sections'])) {
            $game->sections()->delete();
            foreach ($gameData['sections'] as $i => $section) {
                WorldChampionshipGameSection::create([
                    'game_id' => $game->id,
                    'title' => $section['title'] ?? "Section " . ($i + 1),
                    'content' => $section['content'] ?? '',
                    'start_ply' => $section['start_ply'] ?? $section['startPly'] ?? null,
                    'end_ply' => $section['end_ply'] ?? $section['endPly'] ?? null,
                    'key_move_san' => $section['key_move_san'] ?? $section['keyMoveSan'] ?? null,
                    'order' => $section['order'] ?? ($i + 1),
                ]);
            }
        }

        return $game;
    }

    /**
     * Extract 4-digit year from match title.
     */
    private function extractYearFromTitle(string $title): int
    {
        if (preg_match('/\b(18\d\d|19\d\d|20\d\d)\b/', $title, $matches)) {
            return (int)$matches[1];
        }
        return (int)date('Y');
    }

    /**
     * Extract a header tag value from raw PGN string.
     */
    private function extractPgnHeader(?string $pgn, string $header): ?string
    {
        if (empty($pgn)) return null;
        if (preg_match('/\\[' . preg_quote($header, '/') . '\\s+"([^"]+)"\\]/i', $pgn, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
