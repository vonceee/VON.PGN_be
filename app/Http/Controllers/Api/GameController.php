<?php

namespace App\Http\Controllers\Api;

use App\Models\Game;
use App\Services\ChessMicroservice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class GameController
{
    private ChessMicroservice $microservice;

    public function __construct(ChessMicroservice $microservice)
    {
        $this->microservice = $microservice;
    }

    /**
     * Get the current state of a game.
     */
    public function show(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();
        $game = Game::with(['whitePlayer:id,name', 'blackPlayer:id,name'])->find($gameId);

        if (!$game) return response()->json(['message' => 'Game not found'], 404);

        $gameData = $this->microservice->fetchGameState($game);
        if (!$gameData) return response()->json(['message' => 'Game state unavailable'], 410);

        return response()->json([
            'game' => array_merge($game->toDisplayArray($user->id), [
                'fen' => $gameData['fen'],
                'turn' => $gameData['turn'],
                'moves' => $gameData['moves'] ?? [],
                'white_time_remaining_ms' => $gameData['whiteTimeRemainingMs'],
                'black_time_remaining_ms' => $gameData['blackTimeRemainingMs'],
                'server_timestamp' => $gameData['serverTimestamp'] ?? now()->toIso8601String(),
                'legal_moves' => $gameData['legalMoves'] ?? [],
                'bufferCountdown' => $gameData['bufferCountdown'] ?? null,
            ]),
        ]);
    }

    /**
     * Get active game for the authenticated user.
     */
    public function activeGame(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $game = Game::with(['whitePlayer:id,name', 'blackPlayer:id,name'])
                ->where('status', 'active')
                ->where(function ($q) use ($user) {
                    $q->where('white_player_id', $user->id)->orWhere('black_player_id', $user->id);
                })
                ->first();

            if (!$game) return response()->json(['game' => null]);

            $gameData = $this->microservice->fetchGameState($game);
            if (!$gameData) return response()->json(['game' => null]);

            return response()->json([
                'game' => array_merge($game->toDisplayArray($user->id), [
                    'fen' => $gameData['fen'],
                    'turn' => $gameData['turn'],
                    'moves' => $gameData['moves'] ?? [],
                    'white_time_remaining_ms' => $gameData['whiteTimeRemainingMs'],
                    'black_time_remaining_ms' => $gameData['blackTimeRemainingMs'],
                    'server_timestamp' => $gameData['serverTimestamp'] ?? now()->toIso8601String(),
                    'legal_moves' => $gameData['legalMoves'] ?? [],
                    'bufferCountdown' => $gameData['bufferCountdown'] ?? null,
                ]),
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        } catch (\Throwable $e) {
            Log::error('activeGame FAILED: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    /**
     * Resign from the game.
     */
    public function resign(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();
        $response = Http::timeout(5)->post($this->microservice->getUrl() . '/api/resign', [
            'gameId' => $gameId, 'userId' => $user->id
        ]);

        return response()->json($response->json(), $response->status());
    }

    /**
     * Handle draw actions (offer, accept, decline).
     */
    public function draw(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();
        $response = Http::timeout(5)->post($this->microservice->getUrl() . '/api/draw', [
            'gameId' => $gameId, 'userId' => $user->id, 'action' => $request->input('action')
        ]);

        return response()->json($response->json(), $response->status());
    }

    /**
     * Abort the game.
     */
    public function abort(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();
        $response = Http::timeout(5)->post($this->microservice->getUrl() . '/api/abort', [
            'gameId' => $gameId, 'userId' => $user->id
        ]);

        return response()->json($response->json(), $response->status());
    }

    /**
     * Explicitly sync the clock.
     */
    public function syncClock(Request $request, string $gameId): JsonResponse
    {
        $game = Game::find($gameId);
        if (!$game) return response()->json(['message' => 'Not found'], 404);
        
        return response()->json($this->microservice->fetchGameState($game));
    }

    /**
     * Internal API for the microservice to report a completed game.
     */
    public function completeGameInternal(Request $request, string $gameId): JsonResponse
    {

        if ($request->header('X-Internal-Secret') !== config('services.chess.internal_secret')) {
             return response()->json(['message' => 'Unauthorized'], 401);
        }

        $game = Game::with(['whitePlayer', 'blackPlayer'])->find($gameId);
        
        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }


        // If game is already completed/aborted, we check if it already has rating changes saved.
        // If it doesn't have rating changes but we have them now, we should proceed to update!
        if ($game->status !== 'active') {
            if ($game->white_rating_change !== null || $game->status === 'aborted') {
                return response()->json(['success' => true, 'already_processed' => true]);
            }
        }

        DB::transaction(function () use ($request, $game, $gameId) {
            $status = $request->input('status');
            $ratingChanges = $request->input('rating_changes');
            $newRatings = $request->input('new_ratings');

            $game->update([
                'status' => $status,
                'result' => $request->input('result'),
                'termination' => $request->input('termination'),
                'white_rating_change' => $ratingChanges['white'] ?? null,
                'black_rating_change' => $ratingChanges['black'] ?? null,
                'moves' => $request->input('moves'),
            ]);

            if ($status === 'completed' && $ratingChanges && $newRatings) {
                $this->updatePlayerRatings($game, $newRatings);
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Internal endpoint for the microservice to create a new game (rematch).
     */
    public function createGameInternal(Request $request)
    {
        if ($request->header('X-Internal-Secret') !== config('services.chess.internal_secret')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $whitePlayer = \App\Models\User::findOrFail($request->white_id);
        $blackPlayer = \App\Models\User::findOrFail($request->black_id);
        $timeControl = $request->time_control;

        $timeData = $this->parseTimeControl($timeControl);
        $whiteRating = $this->getRatingData($whitePlayer, $timeControl);
        $blackRating = $this->getRatingData($blackPlayer, $timeControl);

        $game = Game::create([
            'white_player_id' => $whitePlayer->id,
            'black_player_id' => $blackPlayer->id,
            'status' => 'active',
            'time_control' => $timeControl,
            'initial_time_ms' => $timeData['initial_time_ms'],
            'increment_ms' => $timeData['increment_ms'],
            'white_elo' => $whiteRating['rating'],
            'black_elo' => $blackRating['rating'],
            'white_rd' => $whiteRating['rd'],
            'black_rd' => $blackRating['rd'],
            'white_vol' => $whiteRating['vol'],
            'black_vol' => $blackRating['vol'],
            'white_last_heartbeat_at' => now(),
            'black_last_heartbeat_at' => now(),
        ]);

        $created = $this->microservice->callWithRetry('/api/create-game', [
            'gameId' => $game->id,
            'whitePlayer' => array_merge($whiteRating, ['userId' => $whitePlayer->id, 'name' => $whitePlayer->name]),
            'blackPlayer' => array_merge($blackRating, ['userId' => $blackPlayer->id, 'name' => $blackPlayer->name]),
            'timeControl' => $timeControl,
            'initialTimeMs' => $timeData['initial_time_ms'],
            'incrementMs' => $timeData['increment_ms']
        ]);

        if (!$created) {
            $game->delete();
            return response()->json(['message' => 'Initialization failed'], 503);
        }

        return response()->json(['game_id' => $game->id, 'message' => 'Rematch game created']);
    }

    /**
     * Internal endpoint for the microservice to register a new Arena match.
     */
    public function createArenaMatchInternal(Request $request): JsonResponse
    {
        if ($request->header('X-Internal-Secret') !== config('services.chess.internal_secret')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $whitePlayer = \App\Models\User::findOrFail($request->white_id);
        $blackPlayer = \App\Models\User::findOrFail($request->black_id);
        $timeControl = $request->time_control || '3+0';
        $arenaId = $request->arena_id;

        $timeData = $this->parseTimeControl($timeControl);
        $whiteRating = $this->getRatingData($whitePlayer, $timeControl);
        $blackRating = $this->getRatingData($blackPlayer, $timeControl);

        $game = Game::create([
            'white_player_id' => $whitePlayer->id,
            'black_player_id' => $blackPlayer->id,
            'status' => 'active',
            'time_control' => $timeControl,
            'initial_time_ms' => $timeData['initial_time_ms'],
            'increment_ms' => $timeData['increment_ms'],
            'white_elo' => $whiteRating['rating'],
            'black_elo' => $blackRating['rating'],
            'white_rd' => $whiteRating['rd'],
            'black_rd' => $blackRating['rd'],
            'white_vol' => $whiteRating['vol'],
            'black_vol' => $blackRating['vol'],
            'white_last_heartbeat_at' => now(),
            'black_last_heartbeat_at' => now(),
            'arena_id' => $arenaId
        ]);

        return response()->json(['game_id' => $game->id, 'message' => 'Arena match registered']);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(Game::with(['whitePlayer:id,name', 'blackPlayer:id,name'])
            ->where(function($q) use ($user) { $q->where('white_player_id', $user->id)->orWhere('black_player_id', $user->id); })
            ->whereIn('status', ['completed', 'aborted'])
            ->orderBy('created_at', 'desc')
            ->paginate(10));
    }

    public function showArchived(Request $request, string $gameId): JsonResponse
    {
        $game = Game::with(['whitePlayer:id,name', 'blackPlayer:id,name'])->findOrFail($gameId);
        return response()->json(['game' => $game]);
    }

    private function updatePlayerRatings(Game $game, array $newRatings): void
    {
        $ratingData = $this->getRatingData($game->whitePlayer, $game->time_control);
        $category = $ratingData['category'];
        

        try {
            foreach (['white' => $game->whitePlayer, 'black' => $game->blackPlayer] as $key => $user) {
                $oldRating = $user->{"{$category}_rating"};
                $user->update([
                    "{$category}_rating" => $newRatings[$key]['rating'],
                    "{$category}_rd" => $newRatings[$key]['rd'],
                    "{$category}_games" => ($user->{"{$category}_games"} ?? 0) + 1,
                    'last_game_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("[Game] Failed to update player ratings: " . $e->getMessage());
        }
    }

    private function parseTimeControl(string $timeControl): array
    {
        $parts = explode('+', $timeControl);
        return [
            'initial_time_ms' => (int)($parts[0] ?? 600) * 1000,
            'increment_ms' => (int)($parts[1] ?? 0) * 1000,
        ];
    }

    private function getRatingData($user, string $timeControl): array
    {
        $parts = explode('+', $timeControl);
        $initial = (int)($parts[0] ?? 600);
        $inc = (int)($parts[1] ?? 0);
        
        // If the initial time is > 3600, it's almost certainly milliseconds (since 3600s = 1 hour)
        if ($initial > 3600) {
            $initial = $initial / 1000;
            $inc = $inc / 1000;
        }

        $totalTime = $initial + ($inc * 40);
        $category = $totalTime < 180 ? 'bullet' : ($totalTime < 600 ? 'blitz' : 'rapid');

        return [
            'category' => $category,
            'rating' => $user->{"{$category}_rating"} ?? 1500,
            'rd' => $user->{"{$category}_rd"} ?? 350,
            'vol' => 0.06,
        ];
    }
}
