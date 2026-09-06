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
 *      and the editorial story builder.
 */
class WorldChampionshipController extends Controller
{
    /**
     * List all World Championship matches.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WorldChampionshipMatch::query()
            ->with(['games' => function ($q) {
                $q->orderBy('order')->orderBy('game_number');
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
     * Create a new World Championship match along with its game and editorial sections.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer',
            'champion' => 'required|string|max:150',
            'challenger' => 'required|string|max:150',
            'winner' => 'required|string|max:150',
            'score' => 'required|string|max:50',
            'format' => 'required|string|max:150',
            'location' => 'required|string|max:255',
            'era' => 'required|string|max:100',
            'description' => 'required|string',
            'key_highlights' => 'nullable|array',
            'study_id' => 'nullable|integer',
            'slug' => 'nullable|string|max:100|unique:world_championship_matches,slug',

            // Nested game payload
            'game' => 'nullable|array',
            'game.title' => 'required_with:game|string|max:255',
            'game.subtitle' => 'nullable|string|max:255',
            'game.white_player' => 'required_with:game|string|max:150',
            'game.black_player' => 'required_with:game|string|max:150',
            'game.result' => 'required_with:game|string|max:10',
            'game.pgn' => 'required_with:game|string',
            'game.eco' => 'nullable|string|max:10',
            'game.opening_name' => 'nullable|string|max:150',
            'game.total_plies' => 'nullable|integer',
            'game.narrative_overview' => 'nullable|string',
            'game.sections' => 'nullable|array',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['title']) . '-' . $validated['year'];
        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (WorldChampionshipMatch::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        DB::beginTransaction();
        try {
            $match = WorldChampionshipMatch::create([
                'slug' => $slug,
                'year' => $validated['year'],
                'display_year' => $validated['display_year'] ?? null,
                'title' => $validated['title'],
                'champion' => $validated['champion'],
                'challenger' => $validated['challenger'],
                'winner' => $validated['winner'],
                'score' => $validated['score'],
                'format' => $validated['format'],
                'location' => $validated['location'],
                'era' => $validated['era'],
                'description' => $validated['description'],
                'key_highlights' => $validated['key_highlights'] ?? [],
                'study_id' => $validated['study_id'] ?? null,
                'games_count' => isset($validated['game']) ? 1 : 0,
            ]);

            if (isset($validated['game'])) {
                $gameData = $validated['game'];
                $gameSlug = Str::slug($gameData['title']) . '-' . $match->id;

                $game = WorldChampionshipGame::create([
                    'match_id' => $match->id,
                    'game_number' => $gameData['game_number'] ?? 1,
                    'slug' => $gameSlug,
                    'title' => $gameData['title'],
                    'subtitle' => $gameData['subtitle'] ?? null,
                    'white_player' => $gameData['white_player'],
                    'black_player' => $gameData['black_player'],
                    'result' => $gameData['result'],
                    'game_date' => $gameData['game_date'] ?? null,
                    'eco' => $gameData['eco'] ?? null,
                    'opening_name' => $gameData['opening_name'] ?? null,
                    'pgn' => $gameData['pgn'],
                    'initial_fen' => $gameData['initial_fen'] ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
                    'total_plies' => $gameData['total_plies'] ?? 0,
                    'narrative_overview' => $gameData['narrative_overview'] ?? null,
                    'is_highlighted' => true,
                    'order' => 1,
                ]);

                if (!empty($gameData['sections'])) {
                    foreach ($gameData['sections'] as $i => $section) {
                        WorldChampionshipGameSection::create([
                            'game_id' => $game->id,
                            'title' => $section['title'] ?? "Section " . ($i + 1),
                            'content' => $section['content'] ?? '',
                            'start_ply' => $section['start_ply'] ?? null,
                            'end_ply' => $section['end_ply'] ?? null,
                            'key_move_san' => $section['key_move_san'] ?? null,
                            'order' => $section['order'] ?? ($i + 1),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'World Championship matchup & editorial story created successfully.',
                'data' => $match->load('games.sections'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create matchup story: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing matchup and its editorial games.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $match = WorldChampionshipMatch::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'champion' => 'sometimes|required|string|max:150',
            'challenger' => 'sometimes|required|string|max:150',
            'winner' => 'sometimes|required|string|max:150',
            'score' => 'sometimes|required|string|max:50',
            'description' => 'sometimes|required|string',
            'game' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $match->update($request->only([
                'title', 'year', 'champion', 'challenger', 'winner',
                'score', 'format', 'location', 'era', 'description',
                'key_highlights', 'study_id'
            ]));

            if ($request->has('game')) {
                $gameData = $request->input('game');
                $game = $match->games()->first();

                if ($game) {
                    $game->update($gameData);
                } else {
                    $game = $match->games()->create($gameData);
                }

                if (isset($gameData['sections'])) {
                    $game->sections()->delete();
                    foreach ($gameData['sections'] as $i => $sec) {
                        $game->sections()->create([
                            'title' => $sec['title'],
                            'content' => $sec['content'],
                            'start_ply' => $sec['start_ply'] ?? null,
                            'end_ply' => $sec['end_ply'] ?? null,
                            'key_move_san' => $sec['key_move_san'] ?? null,
                            'order' => $sec['order'] ?? ($i + 1),
                        ]);
                    }
                }
            }

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
}
