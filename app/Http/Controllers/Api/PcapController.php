<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PcapTeam;
use App\Models\PcapPlayer;
use App\Models\PcapMatch;
use App\Models\PcapStanding;
use Database\Seeders\PcapSeeder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * REST API controller for PCAP League management, matches, and standings.
 * 
 * WHY: Provides database-backed API endpoints for public display and admin operations,
 *      returning camelCase payloads matching Angular Pcap models directly.
 */
class PcapController extends Controller
{
    // ==============================================================
    // TEAMS
    // ==============================================================

    /**
     * List all PCAP teams with their rosters.
     */
    public function indexTeams(Request $request): JsonResponse
    {
        $query = PcapTeam::with('roster');

        if ($request->has('conference') && in_array($request->conference, ['alpha', 'omega'])) {
            $query->where('conference', $request->conference);
        }

        if ($request->has('search') && !empty($request->search)) {
            $s = strtolower($request->search);
            $query->where(function ($q) use ($s) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"])
                  ->orWhereHas('roster', function ($rq) use ($s) {
                      $rq->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"]);
                  });
            });
        }

        $teams = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $teams->map->toFrontendArray(),
            'count' => $teams->count(),
        ])->header('Cache-Control', 'private, max-age=60, must-revalidate');
    }

    /**
     * Show a single team by ID with roster.
     */
    public function showTeam(string $id): JsonResponse
    {
        $team = PcapTeam::with('roster')->find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $team->toFrontendArray(),
        ])->header('Cache-Control', 'private, max-age=60, must-revalidate');
    }

    /**
     * Create a new team with roster and initial standing.
     */
    public function storeTeam(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'conference' => 'required|in:alpha,omega',
            'roster' => 'nullable|array',
            'roster.*.name' => 'nullable|string|max:255',
            'roster.*.title' => 'nullable|string|max:20',
            'roster.*.rating' => 'nullable|integer',
            'roster.*.category' => 'nullable|string|in:O,F,S,HG',
            'roster.*.federation' => 'nullable|string|max:10',
            'roster.*.hometown' => 'nullable|string|max:255',
            'roster.*.winLossRecord' => 'nullable|string|max:50',
        ]);

        $id = !empty($validated['id']) ? Str::slug($validated['id']) : Str::slug($validated['name']);

        // Prevent duplicate ID collision
        if (PcapTeam::where('id', $id)->exists()) {
            $id = $id . '-' . Str::random(4);
        }

        $team = DB::transaction(function () use ($validated, $id) {
            $team = PcapTeam::create([
                'id' => $id,
                'name' => $validated['name'],
                'conference' => $validated['conference'],
                'wins' => 0,
                'losses' => 0,
                'draws' => 0,
                'match_points' => 0,
                'board_points_for' => 0.0,
                'board_points_against' => 0.0,
            ]);

            // Create initial standing row
            PcapStanding::create([
                'team_id' => $team->id,
                'conference' => $team->conference,
                'matches_played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'match_points' => 0,
                'board_points_for' => 0.0,
                'board_points_against' => 0.0,
                'board_points_diff' => 0.0,
                'form' => [],
                'rank' => 99,
            ]);

            // Create roster
            $roster = $validated['roster'] ?? [];
            foreach ($roster as $index => $playerData) {
                if (!empty($playerData['name'])) {
                    PcapPlayer::create([
                        'id' => $playerData['id'] ?? ($team->id . '-p' . ($index + 1)),
                        'team_id' => $team->id,
                        'name' => $playerData['name'],
                        'title' => $playerData['title'] ?? null,
                        'rating' => isset($playerData['rating']) && $playerData['rating'] !== '' ? (int) $playerData['rating'] : null,
                        'category' => $playerData['category'] ?? 'HG',
                        'federation' => $playerData['federation'] ?? 'PHI',
                        'hometown' => $playerData['hometown'] ?? null,
                        'win_loss_record' => $playerData['winLossRecord'] ?? null,
                    ]);
                }
            }

            return $team->load('roster');
        });

        return response()->json([
            'success' => true,
            'message' => 'Team created successfully.',
            'data' => $team->toFrontendArray(),
        ], 201);
    }

    /**
     * Update an existing team and sync roster.
     */
    public function updateTeam(Request $request, string $id): JsonResponse
    {
        $team = PcapTeam::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'conference' => 'sometimes|in:alpha,omega',
            'wins' => 'nullable|integer',
            'losses' => 'nullable|integer',
            'draws' => 'nullable|integer',
            'matchPoints' => 'nullable|integer',
            'boardPointsFor' => 'nullable|numeric',
            'boardPointsAgainst' => 'nullable|numeric',
            'roster' => 'nullable|array',
        ]);

        DB::transaction(function () use ($team, $validated) {
            $updates = [];
            if (isset($validated['name'])) $updates['name'] = $validated['name'];
            if (isset($validated['conference'])) $updates['conference'] = $validated['conference'];
            if (isset($validated['wins'])) $updates['wins'] = $validated['wins'];
            if (isset($validated['losses'])) $updates['losses'] = $validated['losses'];
            if (isset($validated['draws'])) $updates['draws'] = $validated['draws'];
            if (isset($validated['matchPoints'])) $updates['match_points'] = $validated['matchPoints'];
            if (isset($validated['boardPointsFor'])) $updates['board_points_for'] = $validated['boardPointsFor'];
            if (isset($validated['boardPointsAgainst'])) $updates['board_points_against'] = $validated['boardPointsAgainst'];

            $team->update($updates);

            // Update conference in standings if changed
            if (isset($validated['conference'])) {
                PcapStanding::where('team_id', $team->id)->update(['conference' => $validated['conference']]);
            }

            // Sync roster if provided
            if (isset($validated['roster']) && is_array($validated['roster'])) {
                // Delete existing players and re-create to keep roster ordering clean
                PcapPlayer::where('team_id', $team->id)->delete();

                foreach ($validated['roster'] as $index => $p) {
                    if (!empty($p['name'])) {
                        PcapPlayer::create([
                            'id' => $p['id'] ?? ($team->id . '-p' . ($index + 1)),
                            'team_id' => $team->id,
                            'name' => $p['name'],
                            'title' => $p['title'] ?? null,
                            'rating' => isset($p['rating']) && $p['rating'] !== '' ? (int) $p['rating'] : null,
                            'category' => $p['category'] ?? 'HG',
                            'federation' => $p['federation'] ?? 'PHI',
                            'hometown' => $p['hometown'] ?? null,
                            'win_loss_record' => $p['winLossRecord'] ?? null,
                        ]);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Team updated successfully.',
            'data' => $team->fresh('roster')->toFrontendArray(),
        ]);
    }

    /**
     * Delete a team.
     */
    public function destroyTeam(string $id): JsonResponse
    {
        $team = PcapTeam::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $team->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team deleted successfully.',
        ]);
    }

    // ==============================================================
    // MATCHES
    // ==============================================================

    /**
     * List all matchday fixtures.
     */
    public function indexMatches(Request $request): JsonResponse
    {
        $query = PcapMatch::with(['teamA', 'teamB'])->orderBy('round')->orderBy('id');

        if ($request->has('conference') && in_array($request->conference, ['alpha', 'omega', 'interconference'])) {
            $query->where('conference', $request->conference);
        }

        if ($request->has('status') && in_array($request->status, ['upcoming', 'completed'])) {
            $query->where('status', $request->status);
        }

        $matches = $query->get();

        return response()->json([
            'success' => true,
            'data' => $matches->map->toFrontendArray(),
            'count' => $matches->count(),
        ])->header('Cache-Control', 'private, max-age=60, must-revalidate');
    }

    /**
     * Show a single match by ID.
     */
    public function showMatch(string $id): JsonResponse
    {
        $match = PcapMatch::with(['teamA', 'teamB'])->find($id);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match fixture not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $match->toFrontendArray(),
        ])->header('Cache-Control', 'private, max-age=60, must-revalidate');
    }

    /**
     * Create a match fixture.
     */
    public function storeMatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'nullable|string',
            'round' => 'required|integer',
            'conference' => 'required|in:alpha,omega,interconference',
            'season' => 'nullable|string',
            'date' => 'nullable|string',
            'time' => 'nullable|string',
            'status' => 'required|in:upcoming,completed',
            'teamA' => 'required|array',
            'teamA.id' => 'required|string|exists:pcap_teams,id',
            'teamB' => 'required|array',
            'teamB.id' => 'required|string|exists:pcap_teams,id',
            'scoreA' => 'nullable|numeric',
            'scoreB' => 'nullable|numeric',
            'blitzScoreA' => 'nullable|numeric',
            'blitzScoreB' => 'nullable|numeric',
            'rapidScoreA' => 'nullable|numeric',
            'rapidScoreB' => 'nullable|numeric',
            'boards' => 'nullable|array',
        ]);

        $id = $validated['id'] ?? ('match-' . Str::random(8));

        $match = PcapMatch::create([
            'id' => $id,
            'round' => $validated['round'],
            'conference' => $validated['conference'],
            'season' => $validated['season'] ?? '2026 Season • All-Filipino Cup',
            'date' => $validated['date'] ?? 'Upcoming',
            'time' => $validated['time'] ?? '7:00 PM PHT',
            'status' => $validated['status'],
            'team_a_id' => $validated['teamA']['id'],
            'team_b_id' => $validated['teamB']['id'],
            'score_a' => $validated['scoreA'] ?? 0.0,
            'score_b' => $validated['scoreB'] ?? 0.0,
            'blitz_score_a' => $validated['blitzScoreA'] ?? 0.0,
            'blitz_score_b' => $validated['blitzScoreB'] ?? 0.0,
            'rapid_score_a' => $validated['rapidScoreA'] ?? 0.0,
            'rapid_score_b' => $validated['rapidScoreB'] ?? 0.0,
            'boards' => $validated['boards'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Match created successfully.',
            'data' => $match->load(['teamA', 'teamB'])->toFrontendArray(),
        ], 201);
    }

    /**
     * Update a match fixture.
     */
    public function updateMatch(Request $request, string $id): JsonResponse
    {
        $match = PcapMatch::find($id);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match fixture not found.',
            ], 404);
        }

        $validated = $request->validate([
            'round' => 'sometimes|integer',
            'conference' => 'sometimes|in:alpha,omega,interconference',
            'season' => 'nullable|string',
            'date' => 'nullable|string',
            'time' => 'nullable|string',
            'status' => 'sometimes|in:upcoming,completed',
            'teamA' => 'sometimes|array',
            'teamA.id' => 'sometimes|string|exists:pcap_teams,id',
            'teamB' => 'sometimes|array',
            'teamB.id' => 'sometimes|string|exists:pcap_teams,id',
            'scoreA' => 'nullable|numeric',
            'scoreB' => 'nullable|numeric',
            'blitzScoreA' => 'nullable|numeric',
            'blitzScoreB' => 'nullable|numeric',
            'rapidScoreA' => 'nullable|numeric',
            'rapidScoreB' => 'nullable|numeric',
            'boards' => 'nullable|array',
        ]);

        $updates = [];
        if (isset($validated['round'])) $updates['round'] = $validated['round'];
        if (isset($validated['conference'])) $updates['conference'] = $validated['conference'];
        if (isset($validated['season'])) $updates['season'] = $validated['season'];
        if (isset($validated['date'])) $updates['date'] = $validated['date'];
        if (isset($validated['time'])) $updates['time'] = $validated['time'];
        if (isset($validated['status'])) $updates['status'] = $validated['status'];
        if (isset($validated['teamA']['id'])) $updates['team_a_id'] = $validated['teamA']['id'];
        if (isset($validated['teamB']['id'])) $updates['team_b_id'] = $validated['teamB']['id'];
        if (isset($validated['scoreA'])) $updates['score_a'] = $validated['scoreA'];
        if (isset($validated['scoreB'])) $updates['score_b'] = $validated['scoreB'];
        if (isset($validated['blitzScoreA'])) $updates['blitz_score_a'] = $validated['blitzScoreA'];
        if (isset($validated['blitzScoreB'])) $updates['blitz_score_b'] = $validated['blitzScoreB'];
        if (isset($validated['rapidScoreA'])) $updates['rapid_score_a'] = $validated['rapidScoreA'];
        if (isset($validated['rapidScoreB'])) $updates['rapid_score_b'] = $validated['rapidScoreB'];
        if (isset($validated['boards'])) $updates['boards'] = $validated['boards'];

        $match->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'Match updated successfully.',
            'data' => $match->load(['teamA', 'teamB'])->toFrontendArray(),
        ]);
    }

    /**
     * Delete a match fixture.
     */
    public function destroyMatch(string $id): JsonResponse
    {
        $match = PcapMatch::find($id);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match fixture not found.',
            ], 404);
        }

        $match->delete();

        return response()->json([
            'success' => true,
            'message' => 'Match deleted successfully.',
        ]);
    }

    // ==============================================================
    // STANDINGS
    // ==============================================================

    /**
     * List division standings.
     */
    public function indexStandings(Request $request): JsonResponse
    {
        $query = PcapStanding::with('team')
            ->orderBy('match_points', 'desc')
            ->orderBy('board_points_diff', 'desc');

        if ($request->has('conference') && in_array($request->conference, ['alpha', 'omega'])) {
            $query->where('conference', $request->conference);
        }

        $standings = $query->get();

        return response()->json([
            'success' => true,
            'data' => $standings->map->toFrontendArray(),
            'count' => $standings->count(),
        ])->header('Cache-Control', 'private, max-age=60, must-revalidate');
    }

    /**
     * Update a standing row.
     */
    public function updateStanding(Request $request, string $teamId): JsonResponse
    {
        $standing = PcapStanding::where('team_id', $teamId)->first();

        if (!$standing) {
            return response()->json([
                'success' => false,
                'message' => 'Standing row not found for specified team.',
            ], 404);
        }

        $validated = $request->validate([
            'conference' => 'sometimes|in:alpha,omega',
            'matchesPlayed' => 'nullable|integer',
            'won' => 'nullable|integer',
            'drawn' => 'nullable|integer',
            'lost' => 'nullable|integer',
            'matchPoints' => 'nullable|integer',
            'boardPointsFor' => 'nullable|numeric',
            'boardPointsAgainst' => 'nullable|numeric',
            'form' => 'nullable|array',
            'rank' => 'nullable|integer',
        ]);

        $updates = [];
        if (isset($validated['conference'])) $updates['conference'] = $validated['conference'];
        if (isset($validated['matchesPlayed'])) $updates['matches_played'] = $validated['matchesPlayed'];
        if (isset($validated['won'])) $updates['won'] = $validated['won'];
        if (isset($validated['drawn'])) $updates['drawn'] = $validated['drawn'];
        if (isset($validated['lost'])) $updates['lost'] = $validated['lost'];
        if (isset($validated['matchPoints'])) $updates['match_points'] = $validated['matchPoints'];
        if (isset($validated['boardPointsFor'])) $updates['board_points_for'] = $validated['boardPointsFor'];
        if (isset($validated['boardPointsAgainst'])) $updates['board_points_against'] = $validated['boardPointsAgainst'];
        if (isset($validated['form'])) $updates['form'] = $validated['form'];
        if (isset($validated['rank'])) $updates['rank'] = $validated['rank'];

        // Automatically recalculate board diff
        $ptsFor = $updates['board_points_for'] ?? $standing->board_points_for;
        $ptsAgainst = $updates['board_points_against'] ?? $standing->board_points_against;
        $updates['board_points_diff'] = round($ptsFor - $ptsAgainst, 1);

        $standing->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'Standing updated successfully.',
            'data' => $standing->load('team')->toFrontendArray(),
        ]);
    }

    // ==============================================================
    // RESET TO SEED DATA
    // ==============================================================

    /**
     * Reset all PCAP tables back to fresh seeded defaults.
     */
    public function resetData(): JsonResponse
    {
        $seeder = new PcapSeeder();
        $seeder->run();

        return response()->json([
            'success' => true,
            'message' => 'PCAP database reset to initial seeded teams, matches, and standings.',
        ]);
    }
}
