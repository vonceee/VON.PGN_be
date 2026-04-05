<?php

namespace App\Http\Controllers\Api;

use App\Events\GameEnded;
use App\Events\SeekCreated;
use App\Events\SeekRemoved;
use App\Jobs\CheckGameTimeJob;
use App\Models\Game;
use App\Models\GameSeek;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class GameController
{
    private const MICROSERVICE_URL = env('CHESS_MICROSERVICE_URL', 'http://localhost:3006');

    public function __construct() {}

    /**
     * Parse a time control string (e.g., "600+5") into initial time and increment in ms.
     */
    private function parseTimeControl(string $timeControl): array
    {
        $parts = explode('+', $timeControl);
        $baseSeconds = (int) ($parts[0] ?? 600);
        $incrementSeconds = (int) ($parts[1] ?? 0);

        return [
            'initial_time_ms' => $baseSeconds * 1000,
            'increment_ms' => $incrementSeconds * 1000,
        ];
    }

    /**
     * Join the matchmaking queue for a specific time control.
     */
    public function seek(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'time_control' => 'required|string|regex:/^\d+\+\d+$/',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Invalid time control format. Use e.g. "300+0" or "180+2".'], 422);
            }

            $timeControl = $request->input('time_control');
            $user = $request->user();
            $elo = $user->progress?->puzzle_rating ?? 1200;

            \Illuminate\Support\Facades\Log::info('seek called', ['user_id' => $user->id, 'time_control' => $timeControl]);

            // Check if user already has an active game
            $existingGame = Game::with(['whitePlayer:id,name', 'blackPlayer:id,name'])
                ->where('status', 'active')
                ->where(function ($q) use ($user) {
                    $q->where('white_player_id', $user->id)
                      ->orWhere('black_player_id', $user->id);
                })
                ->first();

            if ($existingGame) {
                // Time management handled by microservice
                $legalMoves = []; // Legal moves will come from microservice

                return response()->json([
                    'message' => 'You already have an active game',
                    'game_id' => $existingGame->id,
                    'matched' => true,
                    'existing_game' => [
                        'id' => $existingGame->id,
                        'white_player' => [
                            'id' => $existingGame->whitePlayer->id,
                            'name' => $existingGame->whitePlayer->name,
                        ],
                        'black_player' => [
                            'id' => $existingGame->blackPlayer->id,
                            'name' => $existingGame->blackPlayer->name,
                        ],
                        'status' => $existingGame->status,
                        'time_control' => $existingGame->time_control,
                        'initial_time_ms' => $existingGame->initial_time_ms,
                        'increment_ms' => $existingGame->increment_ms,
                        'result' => $existingGame->result,
                        'termination' => $existingGame->termination,
                        'my_color' => $existingGame->getPlayerColor($user->id),
                        'legal_moves' => $legalMoves,
                    ],
                ]);
            }

            // Cancel any existing seeks for this user before creating a new one
            GameSeek::where('user_id', $user->id)->delete();

            // Create new seek
            $seek = GameSeek::create([
                'user_id' => $user->id,
                'time_control' => $timeControl,
                'elo' => $elo,
            ]);

            try {
                \Illuminate\Support\Facades\Log::info('Broadcasting SeekCreated', ['seek_id' => $seek->id, 'time_control' => $seek->time_control]);
                broadcast(new SeekCreated($seek));
                \Illuminate\Support\Facades\Log::info('SeekCreated broadcast done');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('SeekCreated broadcast failed: ' . $e->getMessage());
            }

            // Immediately try to match within this request
            $opponent = DB::transaction(function () use ($user, $timeControl, $elo) {
                $match = GameSeek::where('time_control', $timeControl)
                    ->where('user_id', '!=', $user->id)
                    ->lockForUpdate()
                    ->orderByRaw('ABS(elo - ?)', [$elo])
                    ->first();

                if (!$match) {
                    return null;
                }

                $matchedSeekId = $match->id;
                $opponent = $match->user;
                $match->delete();

                return ['opponent' => $opponent, 'matchedSeekId' => $matchedSeekId];
            });

            if ($opponent) {
                $matchedSeekId = $opponent['matchedSeekId'] ?? null;
                $opponentUser = $opponent['opponent'] ?? null;

                // Remove own seek too
                $userSeekId = GameSeek::where('user_id', $user->id)->where('time_control', $timeControl)->value('id');
                GameSeek::where('user_id', $user->id)->where('time_control', $timeControl)->delete();

                if ($matchedSeekId) {
                    try {
                        broadcast(new SeekRemoved($matchedSeekId, 'matched'));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('SeekRemoved (matched) broadcast failed: ' . $e->getMessage());
                    }
                }
                if ($userSeekId) {
                    try {
                        broadcast(new SeekRemoved($userSeekId, 'matched'));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('SeekRemoved (user matched) broadcast failed: ' . $e->getMessage());
                    }
                }

                // Randomize colors
                $whiteId = rand(0, 1) ? $user->id : $opponentUser->id;
                $blackId = $whiteId === $user->id ? $opponentUser->id : $user->id;

                $timeData = $this->parseTimeControl($timeControl);

                $game = Game::create([
                    'white_player_id' => $whiteId,
                    'black_player_id' => $blackId,
                    'status' => 'active',
                    'time_control' => $timeControl,
                    'initial_time_ms' => $timeData['initial_time_ms'],
                    'increment_ms' => $timeData['increment_ms'],
                    'white_time_remaining_ms' => $timeData['initial_time_ms'],
                    'black_time_remaining_ms' => $timeData['initial_time_ms'],
                    'turn' => 'white',
                    'moves' => [],
                    'white_elo' => $user->progress?->puzzle_rating ?? 1200,
                    'black_elo' => $opponentUser->progress?->puzzle_rating ?? 1200,
                    'white_last_heartbeat_at' => now(),
                    'black_last_heartbeat_at' => now(),
                ]);

                \Illuminate\Support\Facades\Log::info('Game created', ['game_id' => $game->id]);

                // Create game in microservice
                try {
                    $microserviceResponse = Http::timeout(5)->post(self::MICROSERVICE_URL . '/api/create-game', [
                        'gameId' => $game->id,
                        'whitePlayer' => [
                            'userId' => $whiteId,
                            'socketId' => '', // Will be set when players connect
                            'name' => $whiteId === $user->id ? $user->name : $opponentUser->name
                        ],
                        'blackPlayer' => [
                            'userId' => $blackId,
                            'socketId' => '', // Will be set when players connect
                            'name' => $blackId === $user->id ? $user->name : $opponentUser->name
                        ],
                        'timeControl' => $timeControl,
                        'initialTimeMs' => $timeData['initial_time_ms'],
                        'incrementMs' => $timeData['increment_ms']
                    ]);

                    if ($microserviceResponse->successful()) {
                        \Illuminate\Support\Facades\Log::info('Game created in microservice', ['game_id' => $game->id]);
                    } else {
                        \Illuminate\Support\Facades\Log::error('Failed to create game in microservice: ' . $microserviceResponse->body());
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Microservice create-game error: ' . $e->getMessage());
                }

                try {
                    broadcast(new \App\Events\GameMatched($game));
                    \Illuminate\Support\Facades\Log::info('GameMatched broadcasted', ['game_id' => $game->id]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('GameMatched broadcast failed: ' . $e->getMessage(), [
                        'game_id' => $game->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                return response()->json([
                    'message' => 'Match found!',
                    'game_id' => $game->id,
                    'matched' => true,
                ]);
            }

            return response()->json([
                'message' => 'Searching for opponent...',
                'time_control' => $timeControl,
                'matched' => false,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('seek FAILED: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel matchmaking search.
     */
    public function cancelSeek(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'time_control' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Time control required'], 422);
        }

        $user = $request->user();
        $seek = GameSeek::where('user_id', $user->id)
            ->where('time_control', $request->input('time_control'))
            ->first();

        $seekId = $seek?->id;
        $seek?->delete();

        if ($seekId) {
            try {
                broadcast(new SeekRemoved($seekId, 'cancelled'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('SeekRemoved broadcast failed: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Removed from queue']);
    }

    /**
     * List all active seeks.
     */
    public function seeks(): JsonResponse
    {
        $seeks = GameSeek::with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($seek) => [
                'id' => $seek->id,
                'user_id' => $seek->user_id,
                'username' => $seek->user?->name,
                'elo' => $seek->elo,
                'time_control' => $seek->time_control,
                'created_at' => $seek->created_at?->toISOString(),
            ]);

        return response()->json(['seeks' => $seeks]);
    }

    /**
     * Join a specific seek (accept someone's seek).
     */
    public function joinSeek(Request $request, int $seekId): JsonResponse
    {
        try {
            $user = $request->user();

            // Find the seek
            $seek = GameSeek::find($seekId);

            if (!$seek) {
                return response()->json(['message' => 'Seek not found'], 404);
            }

            // Can't join own seek
            if ($seek->user_id === $user->id) {
                return response()->json(['message' => 'Cannot join your own seek'], 400);
            }

            // Check if user already has an active game
            $existingGame = Game::where('status', 'active')
                ->where(function ($q) use ($user) {
                    $q->where('white_player_id', $user->id)
                      ->orWhere('black_player_id', $user->id);
                })
                ->first();

            if ($existingGame) {
                // Time management handled by microservice
                $legalMoves = []; // Legal moves will come from microservice

                return response()->json([
                    'message' => 'You already have an active game',
                    'game_id' => $existingGame->id,
                    'matched' => true,
                    'existing_game' => [
                        'id' => $existingGame->id,
                        'white_player' => [
                            'id' => $existingGame->whitePlayer->id,
                            'name' => $existingGame->whitePlayer->name,
                        ],
                        'black_player' => [
                            'id' => $existingGame->blackPlayer->id,
                            'name' => $existingGame->blackPlayer->name,
                        ],
                        'status' => $existingGame->status,
                        'time_control' => $existingGame->time_control,
                        'initial_time_ms' => $existingGame->initial_time_ms,
                        'increment_ms' => $existingGame->increment_ms,
                        'result' => $existingGame->result,
                        'termination' => $existingGame->termination,
                        'my_color' => $existingGame->getPlayerColor($user->id),
                        'legal_moves' => $legalMoves,
                    ],
                ]);
            }

            $opponentUser = $seek->user;
            $timeControl = $seek->time_control;
            $elo = $user->progress?->puzzle_rating ?? 1200;

            // Delete the accepted seek
            $seek->delete();

            try {
                broadcast(new SeekRemoved($seekId, 'matched'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('SeekRemoved broadcast failed: ' . $e->getMessage());
            }

            // Randomize colors
            $whiteId = rand(0, 1) ? $user->id : $opponentUser->id;
            $blackId = $whiteId === $user->id ? $opponentUser->id : $user->id;

            $timeData = $this->parseTimeControl($timeControl);

            $game = Game::create([
                'white_player_id' => $whiteId,
                'black_player_id' => $blackId,
                'status' => 'active',
                'time_control' => $timeControl,
                'initial_time_ms' => $timeData['initial_time_ms'],
                'increment_ms' => $timeData['increment_ms'],
                'white_time_remaining_ms' => $timeData['initial_time_ms'],
                'black_time_remaining_ms' => $timeData['initial_time_ms'],
                'turn' => 'white',
                'moves' => [],
                'white_elo' => $user->progress?->puzzle_rating ?? 1200,
                'black_elo' => $opponentUser->progress?->puzzle_rating ?? 1200,
                'white_last_heartbeat_at' => now(),
                'black_last_heartbeat_at' => now(),
            ]);

            // Create game in microservice
            try {
                $microserviceResponse = Http::timeout(5)->post(self::MICROSERVICE_URL . '/api/create-game', [
                    'gameId' => $game->id,
                    'whitePlayer' => [
                        'userId' => $whiteId,
                        'socketId' => '', // Will be set when players connect
                        'name' => $whiteId === $user->id ? $user->name : $opponentUser->name
                    ],
                    'blackPlayer' => [
                        'userId' => $blackId,
                        'socketId' => '', // Will be set when players connect
                        'name' => $blackId === $user->id ? $user->name : $opponentUser->name
                    ],
                    'timeControl' => $timeControl,
                    'initialTimeMs' => $timeData['initial_time_ms'],
                    'incrementMs' => $timeData['increment_ms']
                ]);

                if ($microserviceResponse->successful()) {
                    \Illuminate\Support\Facades\Log::info('Game created in microservice', ['game_id' => $game->id]);
                } else {
                    \Illuminate\Support\Facades\Log::error('Failed to create game in microservice: ' . $microserviceResponse->body());
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Microservice create-game error: ' . $e->getMessage());
            }

            try {
                broadcast(new \App\Events\GameMatched($game));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('GameMatched broadcast failed: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Match found!',
                'game_id' => $game->id,
                'matched' => true,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('joinSeek FAILED: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the current state of a game.
     */
    public function show(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();

        $game = Game::with(['whitePlayer:id,name', 'blackPlayer:id,name'])->find($gameId);

        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        if (!$game->isPlayer($user->id)) {
            return response()->json(['message' => 'Not authorized to view this game'], 403);
        }

        // Time management handled by microservice
        $legalMoves = []; // Legal moves will come from microservice

        return response()->json([
            'game' => [
                'id' => $game->id,
                'white_player' => [
                    'id' => $game->whitePlayer->id,
                    'name' => $game->whitePlayer->name,
                ],
                'black_player' => [
                    'id' => $game->blackPlayer->id,
                    'name' => $game->blackPlayer->name,
                ],
                'status' => $game->status,
                'time_control' => $game->time_control,
                'initial_time_ms' => $game->initial_time_ms,
                'increment_ms' => $game->increment_ms,
                'result' => $game->result,
                'termination' => $game->termination,
                'my_color' => $game->getPlayerColor($user->id),
                'legal_moves' => $legalMoves,

            ],
        ]);
    }



    /**
     * Play a move - proxy to microservice.
     */
    public function move(Request $request, string $gameId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'move' => 'required|string|regex:/^[a-h][1-8][a-h][1-8][qrnb]?$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid move format. Use UCI notation, e.g. "e2e4".'], 422);
        }

        $user = $request->user();
        $uciMove = $request->input('move');

        // Check if game exists in DB (basic validation)
        $game = Game::find($gameId);
        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        if (!$game->isPlayer($user->id)) {
            return response()->json(['message' => 'You are not a player in this game'], 403);
        }

        // Proxy to microservice for all game logic
        try {
            $response = Http::timeout(10)->post(self::MICROSERVICE_URL . '/api/move', [
                'gameId' => $gameId,
                'userId' => $user->id,
                'move' => $uciMove,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Update local game state for database persistence
                if ($game && isset($data['status'])) {
                    $updateData = [
                        'current_fen' => $data['fen'] ?? $game->current_fen,
                        'moves' => array_merge($game->moves ?? [], [$uciMove]),
                        'turn' => $data['turn'] ?? $game->turn,
                        'status' => $data['status'],
                    ];

                    if ($data['status'] === 'completed') {
                        $updateData['result'] = $data['result'];
                        $updateData['termination'] = $data['termination'];
                        // Broadcast game ended via Laravel events
                        broadcast(new GameEnded($game));
                    }

                    $game->update($updateData);
                }

                return response()->json($data);
            }

            return response()->json(['message' => $response->body()], $response->status());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Microservice proxy error: ' . $e->getMessage());
            return response()->json(['message' => 'Service temporarily unavailable'], 503);
        }
    }

    /**
     * Resign from a game - proxy to microservice.
     */
    public function resign(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();

        // Proxy to microservice
        try {
            $response = Http::timeout(10)->post(self::MICROSERVICE_URL . '/api/resign', [
                'gameId' => $gameId,
                'userId' => $user->id,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Update local game state
                $game = Game::find($gameId);
                if ($game) {
                    $game->update([
                        'status' => 'completed',
                        'result' => $data['result'] ?? null,
                        'termination' => 'resignation',
                    ]);

                    broadcast(new GameEnded($game));
                }

                return response()->json($data);
            }

            return response()->json(['message' => $response->body()], $response->status());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Microservice proxy error: ' . $e->getMessage());
            return response()->json(['message' => 'Service temporarily unavailable'], 503);
        }
    }

    /**
     * Abort a game before any move has been made.
     */
    public function abort(Request $request, string $gameId): JsonResponse
    {
        $game = Game::find($gameId);
        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        $user = $request->user();

        if (!$game->isActive()) {
            return response()->json(['message' => 'Game is not active'], 422);
        }

        if (!$game->isPlayer($user->id)) {
            return response()->json(['message' => 'You are not a player in this game'], 403);
        }

        $moves = $game->moves ?? [];
        if (count($moves) > 0) {
            return response()->json(['message' => 'Cannot abort after a move has been made. Use resign instead.'], 422);
        }

        $game->update([
            'status' => 'aborted',
            'result' => null,
            'termination' => 'aborted',
        ]);

        broadcast(new GameEnded($game));

        return response()->json([
            'message' => 'Game aborted',
            'termination' => 'aborted',
        ]);
    }

    /**
     * Offer, accept, or decline a draw - proxy to microservice.
     */
    public function draw(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();
        $action = $request->input('action', 'offer');

        // Proxy to microservice
        try {
            $response = Http::timeout(10)->post(self::MICROSERVICE_URL . '/api/draw', [
                'gameId' => $gameId,
                'userId' => $user->id,
                'action' => $action,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Update local game state if needed
                $game = Game::find($gameId);
                if ($game && isset($data['result'])) {
                    $game->update([
                        'status' => 'completed',
                        'result' => $data['result'],
                        'termination' => $data['termination'] ?? 'agreement',
                        'draw_offered_by' => null,
                        'draw_offered_at' => null,
                    ]);

                    broadcast(new GameEnded($game));
                }

                return response()->json($data);
            }

            return response()->json(['message' => $response->body()], $response->status());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Microservice proxy error: ' . $e->getMessage());
            return response()->json(['message' => 'Service temporarily unavailable'], 503);
        }
    }

    /**
     * Request clock synchronization - proxy to microservice.
     */
    public function syncClock(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();

        // Proxy to microservice
        try {
            $response = Http::timeout(10)->post(self::MICROSERVICE_URL . '/api/sync-clock', [
                'gameId' => $gameId,
                'userId' => $user->id,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['message' => $response->body()], $response->status());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Microservice proxy error: ' . $e->getMessage());
            return response()->json(['message' => 'Service temporarily unavailable'], 503);
        }
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
                    $q->where('white_player_id', $user->id)
                      ->orWhere('black_player_id', $user->id);
                })
                ->first();

            $game = Game::with(['whitePlayer:id,name', 'blackPlayer:id,name'])
                ->where('status', 'active')
                ->where(function ($q) use ($user) {
                    $q->where('white_player_id', $user->id)
                      ->orWhere('black_player_id', $user->id);
                })
                ->first();

            if (!$game) {
                return response()->json(['game' => null]);
            }

            // Time management handled by microservice
            $legalMoves = []; // Legal moves will come from microservice

            return response()->json([
                'game' => [
                    'id' => $game->id,
                    'white_player' => [
                        'id' => $game->whitePlayer->id,
                        'name' => $game->whitePlayer->name,
                    ],
                    'black_player' => [
                        'id' => $game->blackPlayer->id,
                        'name' => $game->blackPlayer->name,
                    ],
                    'status' => $game->status,
                    'time_control' => $game->time_control,
                    'initial_time_ms' => $game->initial_time_ms,
                    'increment_ms' => $game->increment_ms,
                    'result' => $game->result,
                    'termination' => $game->termination,
                    'my_color' => $game->getPlayerColor($user->id),
                    'legal_moves' => $legalMoves,

                ],
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('activeGame FAILED: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Player heartbeat to indicate they're still connected.
     */
    public function heartbeat(Request $request, string $gameId): JsonResponse
    {
        $game = Game::find($gameId);
        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        $user = $request->user();

        if (!$game->isPlayer($user->id)) {
            return response()->json(['message' => 'Not authorized'], 403);
        }

        $playerColor = $game->getPlayerColor($user->id);
        
        $updateField = $playerColor === 'white' ? 'white_last_heartbeat_at' : 'black_last_heartbeat_at';
        $game->update([$updateField => now()]);



        return response()->json(['message' => 'Heartbeat recorded']);
    }


}
