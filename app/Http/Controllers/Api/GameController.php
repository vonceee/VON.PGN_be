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
    private string $microserviceUrl;

    public function __construct()
    {
        // Use 127.0.0.1 for development to avoid IPv6/IPv4 resolution issues
        $defaultUrl = app()->environment('local')
            ? 'http://127.0.0.1:3006'
            : 'https://von-pgn-microservice.onrender.com';

        $this->microserviceUrl = env('CHESS_MICROSERVICE_URL', $defaultUrl);
    }

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
            $ratingData = $this->getRatingData($user, $timeControl);
            $elo = $ratingData['rating'];

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
                // Get real game state from microservice
                $gameData = $this->fetchGameState($existingGame);

                if (!$gameData) {
                    // Try to proceed if it was just a ghost game we just handled
                    return $this->seek($request);
                }

                if ($existingGame->status === 'active') {
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
                            'fen' => $gameData['fen'],
                            'turn' => $gameData['turn'],
                            'moves' => $gameData['moves'] ?? [],
                            'white_time_remaining_ms' => $gameData['whiteTimeRemainingMs'],
                            'black_time_remaining_ms' => $gameData['blackTimeRemainingMs'],
                            'server_timestamp' => $gameData['serverTimestamp'] ?? now()->toIso8601String(),
                            'result' => $existingGame->result,
                            'termination' => $existingGame->termination,
                            'my_color' => $existingGame->getPlayerColor($user->id),
                            'legal_moves' => $gameData['legalMoves'] ?? [],
                            'bufferCountdown' => $gameData['bufferCountdown'] ?? null,
                        ],
                    ]);
                }
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

                $whitePlayer = $whiteId === $user->id ? $user : $opponentUser;
                $blackPlayer = $blackId === $user->id ? $user : $opponentUser;

                $whiteRatingData = $this->getRatingData($whitePlayer, $timeControl);
                $blackRatingData = $this->getRatingData($blackPlayer, $timeControl);

                $game = Game::create([
                    'white_player_id' => $whiteId,
                    'black_player_id' => $blackId,
                    'status' => 'active',
                    'time_control' => $timeControl,
                    'initial_time_ms' => $timeData['initial_time_ms'],
                    'increment_ms' => $timeData['increment_ms'],
                    'white_elo' => $whiteRatingData['rating'],
                    'black_elo' => $blackRatingData['rating'],
                    'white_rd' => $whiteRatingData['rd'],
                    'black_rd' => $blackRatingData['rd'],
                    'white_vol' => $whiteRatingData['vol'],
                    'black_vol' => $blackRatingData['vol'],
                    'white_last_heartbeat_at' => now(),
                    'black_last_heartbeat_at' => now(),
                ]);

                \Illuminate\Support\Facades\Log::info('Game created', ['game_id' => $game->id]);

                // Create game in microservice with retries for cold-start
                $created = $this->callMicroserviceWithRetry('/api/create-game', [
                    'gameId' => $game->id,
                    'whitePlayer' => [
                        'userId' => $whiteId,
                        'socketId' => '', 
                        'name' => $whiteId === $user->id ? $user->name : $opponentUser->name,
                        'rating' => $whiteRatingData['rating'],
                        'rd' => $whiteRatingData['rd'],
                        'vol' => $whiteRatingData['vol']
                    ],
                    'blackPlayer' => [
                        'userId' => $blackId,
                        'socketId' => '', 
                        'name' => $blackId === $user->id ? $user->name : $opponentUser->name,
                        'rating' => $blackRatingData['rating'],
                        'rd' => $blackRatingData['rd'],
                        'vol' => $blackRatingData['vol']
                    ],
                    'timeControl' => $timeControl,
                    'initialTimeMs' => $timeData['initial_time_ms'],
                    'incrementMs' => $timeData['increment_ms']
                ], 'POST');

                if (!$created) {
                    \Illuminate\Support\Facades\Log::error('Failed to create game in microservice after retries', ['game_id' => $game->id]);
                    return response()->json(['message' => 'Chess microservice is currently unavailable. Please try again later.'], 503);
                }
                
                \Illuminate\Support\Facades\Log::info('Game created in microservice', ['game_id' => $game->id]);

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
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
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
                // Get real game state from microservice
                $gameData = $this->fetchGameState($existingGame);

                if (!$gameData) {
                    // Try to proceed if it was just a ghost game
                    return $this->joinSeek($request, $seekId);
                }

                if ($existingGame->status === 'active') {
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
                            'fen' => $gameData['fen'],
                            'turn' => $gameData['turn'],
                            'moves' => $gameData['moves'] ?? [],
                            'white_time_remaining_ms' => $gameData['whiteTimeRemainingMs'],
                            'black_time_remaining_ms' => $gameData['blackTimeRemainingMs'],
                            'server_timestamp' => $gameData['serverTimestamp'] ?? now()->toIso8601String(),
                            'result' => $existingGame->result,
                            'termination' => $existingGame->termination,
                            'my_color' => $existingGame->getPlayerColor($user->id),
                            'legal_moves' => $gameData['legalMoves'] ?? [],
                            'bufferCountdown' => $gameData['bufferCountdown'] ?? null,
                        ],
                    ]);
                }
            }

            $opponentUser = $seek->user;
            $timeControl = $seek->time_control;
            $ratingData = $this->getRatingData($user, $timeControl);
            $elo = $ratingData['rating'];

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

            $whitePlayer = $whiteId === $user->id ? $user : $opponentUser;
            $blackPlayer = $blackId === $user->id ? $user : $opponentUser;

            $whiteRatingData = $this->getRatingData($whitePlayer, $timeControl);
            $blackRatingData = $this->getRatingData($blackPlayer, $timeControl);

            $game = Game::create([
                'white_player_id' => $whiteId,
                'black_player_id' => $blackId,
                'status' => 'active',
                'time_control' => $timeControl,
                'initial_time_ms' => $timeData['initial_time_ms'],
                'increment_ms' => $timeData['increment_ms'],
                'white_elo' => $whiteRatingData['rating'],
                'black_elo' => $blackRatingData['rating'],
                'white_rd' => $whiteRatingData['rd'],
                'black_rd' => $blackRatingData['rd'],
                'white_vol' => $whiteRatingData['vol'],
                'black_vol' => $blackRatingData['vol'],
                'white_last_heartbeat_at' => now(),
                'black_last_heartbeat_at' => now(),
            ]);

            // Create game in microservice
            try {
                $microserviceResponse = Http::timeout(5)->post($this->microserviceUrl . '/api/create-game', [
                    'gameId' => $game->id,
                    'whitePlayer' => [
                        'userId' => $whiteId,
                        'socketId' => '', 
                        'name' => $whiteId === $user->id ? $user->name : $opponentUser->name,
                        'rating' => $whiteRatingData['rating'],
                        'rd' => $whiteRatingData['rd'],
                        'vol' => $whiteRatingData['vol']
                    ],
                    'blackPlayer' => [
                        'userId' => $blackId,
                        'socketId' => '', 
                        'name' => $blackId === $user->id ? $user->name : $opponentUser->name,
                        'rating' => $blackRatingData['rating'],
                        'rd' => $blackRatingData['rd'],
                        'vol' => $blackRatingData['vol']
                    ],
                    'timeControl' => $timeControl,
                    'initialTimeMs' => $timeData['initial_time_ms'],
                    'incrementMs' => $timeData['increment_ms']
                ]);

                if ($microserviceResponse->successful()) {
                    \Illuminate\Support\Facades\Log::info('Game created in microservice', ['game_id' => $game->id]);
                } else {
                    \Illuminate\Support\Facades\Log::error('Failed to create game in microservice: ' . $microserviceResponse->body());
                    return response()->json(['message' => 'Chess microservice is currently unavailable. Please try again later.'], 503);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Microservice create-game error: ' . $e->getMessage());
                return response()->json(['message' => 'Chess microservice is currently unavailable. Please try again later.'], 503);
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

        // Get real game state from microservice
        $gameData = $this->fetchGameState($game);

        if (!$gameData) {
            return response()->json(['message' => 'Game state is no longer available.'], 410);
        }

        return response()->json([
            'game' => [
                'id' => $game->id,
                'white_player' => [
                    'id' => $game->whitePlayer->id,
                    'name' => $game->whitePlayer->name,
                    'rating' => $game->white_elo,
                ],
                'black_player' => [
                    'id' => $game->blackPlayer->id,
                    'name' => $game->blackPlayer->name,
                    'rating' => $game->black_elo,
                ],
                'status' => $game->status,
                'time_control' => $game->time_control,
                'initial_time_ms' => $game->initial_time_ms,
                'increment_ms' => $game->increment_ms,
                'fen' => $gameData['fen'],
                'turn' => $gameData['turn'],
                'moves' => $gameData['moves'] ?? [],
                'white_time_remaining_ms' => $gameData['whiteTimeRemainingMs'],
                'black_time_remaining_ms' => $gameData['blackTimeRemainingMs'],
                'server_timestamp' => $gameData['serverTimestamp'] ?? now()->toIso8601String(),
                'result' => $game->result,
                'termination' => $game->termination,
                'white_rating_change' => $game->white_rating_change,
                'black_rating_change' => $game->black_rating_change,
                'my_color' => $game->getPlayerColor($user->id),
                'legal_moves' => $gameData['legalMoves'] ?? [],
                'bufferCountdown' => $gameData['bufferCountdown'] ?? null,

            ],
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
                    $q->where('white_player_id', $user->id)
                      ->orWhere('black_player_id', $user->id);
                })
                ->first();

            if (!$game) {
                return response()->json(['game' => null]);
            }

            // Get real game state from microservice
            $gameData = $this->fetchGameState($game);

            if (!$gameData) {
                return response()->json(['game' => null]);
            }

            return response()->json([
                'game' => [
                    'id' => $game->id,
                    'white_player' => [
                        'id' => $game->whitePlayer->id,
                        'name' => $game->whitePlayer->name,
                        'rating' => $game->white_elo,
                    ],
                    'black_player' => [
                        'id' => $game->blackPlayer->id,
                        'name' => $game->blackPlayer->name,
                        'rating' => $game->black_elo,
                    ],
                    'status' => $game->status,
                    'time_control' => $game->time_control,
                    'initial_time_ms' => $game->initial_time_ms,
                    'increment_ms' => $game->increment_ms,
                    'fen' => $gameData['fen'],
                    'turn' => $gameData['turn'],
                    'moves' => $gameData['moves'] ?? [],
                    'white_time_remaining_ms' => $gameData['whiteTimeRemainingMs'],
                    'black_time_remaining_ms' => $gameData['blackTimeRemainingMs'],
                    'server_timestamp' => $gameData['serverTimestamp'] ?? now()->toIso8601String(),
                    'result' => $game->result,
                    'termination' => $game->termination,
                    'white_rating_change' => $game->white_rating_change,
                    'black_rating_change' => $game->black_rating_change,
                    'my_color' => $game->getPlayerColor($user->id),
                    'legal_moves' => $gameData['legalMoves'] ?? [],
                    'bufferCountdown' => $gameData['bufferCountdown'] ?? null,

                ],
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // Return proper 503 for microservice unavailability
            return response()->json(['message' => $e->getMessage()], 503);
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
     * Resign from the game.
     */
    public function resign(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();
        $response = Http::timeout(5)->post($this->microserviceUrl . '/api/resign', [
            'gameId' => $gameId,
            'userId' => $user->id
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['message' => 'Action failed'], $response->status());
    }

    /**
     * Handle draw actions (offer, accept, decline).
     */
    public function draw(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();
        $action = $request->input('action');

        $response = Http::timeout(5)->post($this->microserviceUrl . '/api/draw', [
            'gameId' => $gameId,
            'userId' => $user->id,
            'action' => $action
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['message' => 'Action failed'], $response->status());
    }

    /**
     * Abort the game.
     */
    public function abort(Request $request, string $gameId): JsonResponse
    {
        $user = $request->user();
        $response = Http::timeout(5)->post($this->microserviceUrl . '/api/abort', [
            'gameId' => $gameId,
            'userId' => $user->id
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['message' => 'Action failed'], $response->status());
    }

    /**
     * Explicitly sync the clock.
     */
    public function syncClock(Request $request, string $gameId): JsonResponse
    {
        $game = Game::find($gameId);
        if (!$game) return response()->json(['message' => 'Not found'], 404);
        
        $gameData = $this->fetchGameState($game);
        return response()->json($gameData);
    }

    /**
     * Helper to fetch game state from microservice and handle synchronization.
     * Includes retries for cold-start scenarios where microservice is waking up.
     */
    private function fetchGameState(Game $game): ?array
    {
        $url = $this->microserviceUrl . '/api/games/' . $game->id;
        $maxRetries = 5;
        $lastError = null;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                \Illuminate\Support\Facades\Log::info('Fetching game state from microservice', [
                    'game_id' => $game->id,
                    'attempt' => $attempt,
                    'url' => $url
                ]);
                
                // Longer timeouts for cold-start: 5s, 10s, 15s, 15s, 15s
                $timeout = $attempt < 4 ? $attempt * 5 : 15;
                $response = Http::timeout($timeout)->get($url);

                if ($response->successful()) {
                    $gameData = $response->json();
                    
                    // Authoritative Sync: If microservice says game is done (completed/aborted), update Laravel DB
                    $isFinished = in_array($gameData['status'] ?? '', ['completed', 'aborted']);
                    if ($isFinished && $game->status !== $gameData['status']) {
                        $game->update([
                            'status' => $gameData['status'],
                            'result' => $gameData['result'] ?? null,
                            'termination' => $gameData['termination'] ?? null,
                        ]);
                        
                        // Trigger broadcast for frontend
                        try {
                            broadcast(new \App\Events\GameEnded($game));
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning('GameEnded broadcast failed in fetchGameState: ' . $e->getMessage());
                        }
                    }
                    
                    return $gameData;
                }

                if ($response->status() === 404) {
                    \Illuminate\Support\Facades\Log::warning('Game missing from microservice. Marking as abandoned in DB.', [
                        'game_id' => $game->id
                    ]);
                    
                    // End the game in DB as it's no longer in the microservice's memory
                    $game->update([
                        'status' => 'completed',
                        'result' => null,
                        'termination' => 'abandoned'
                    ]);
                    
                    return null;
                }

                // Non-502 error - fail immediately
                \Illuminate\Support\Facades\Log::error('Microservice returnedHTTP error', [
                    'game_id' => $game->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $url
                ]);
                
                throw new \Exception('Microservice returned HTTP ' . $response->status());
                
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Connection errors (502, 503, timeout, connection refused) - retry
                $lastError = $e->getMessage();
                \Illuminate\Support\Facades\Log::warning('Microservice connection failed (attempt ' . $attempt . '/' . $maxRetries . ')', [
                    'game_id' => $game->id,
                    'error' => $e->getMessage(),
                    'url' => $url
                ]);
                
                if ($attempt < $maxRetries) {
                    // Longer backoff for cold-start: 2s, 4s, 6s, 8s (total wait: 20s)
                    usleep($attempt * 2000000);
                }
                
            } catch (\Exception $e) {
                // Other errors - fail immediately
                throw $e;
            }
        }
        
        // All retries exhausted
        \Illuminate\Support\Facades\Log::error('Microservice communication failed after retries', [
            'game_id' => $game->id,
            'last_error' => $lastError,
            'url' => $url
        ]);
        
        // Throw a specific exception so callers can handle it and return proper 503
        throw new \Symfony\Component\HttpKernel\Exception\HttpException(503, 'Chess microservice is currently unavailable. Please try again later.');
    }
    
/**
      * Call microservice endpoint with retry logic for cold-start scenarios.
      */
    private function callMicroserviceWithRetry(string $endpoint, array $data, string $method = 'POST'): bool
    {
        $url = $this->microserviceUrl . $endpoint;
        $maxRetries = 5;
        $lastError = null;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                \Illuminate\Support\Facades\Log::info('Calling microservice', [
                    'endpoint' => $endpoint,
                    'attempt' => $attempt,
                    'method' => $method
                ]);
                
                // Longer timeouts for cold-start: 5s, 10s, 15s, 15s, 15s
                $timeout = $attempt < 4 ? $attempt * 5 : 15;
                $response = $method === 'POST' 
                    ? Http::timeout($timeout)->post($url, $data)
                    : Http::timeout($timeout)->get($url);

                if ($response->successful()) {
                    return true;
                }

                // Non-connection error - fail immediately
                \Illuminate\Support\Facades\Log::error('Microservice returned HTTP error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return false;
                
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastError = $e->getMessage();
                \Illuminate\Support\Facades\Log::warning('Microservice connection failed (attempt ' . $attempt . '/' . $maxRetries . ')', [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
                
                if ($attempt < $maxRetries) {
                    // Longer backoff for cold-start: 2s, 4s, 6s, 8s (total wait: 20s)
                    usleep($attempt * 2000000);
                }
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Microservice call failed', [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
                return false;
            }
        }
        
        \Illuminate\Support\Facades\Log::error('Microservice call failed after retries', [
            'endpoint' => $endpoint,
            'last_error' => $lastError
        ]);
        
        return false;
    }

    /**
     * Get the rating category and values for a user based on time control.
     */
    private function getRatingData($user, string $timeControl): array
    {
        $parts = explode('+', $timeControl);
        $baseSeconds = (int) ($parts[0] ?? 600);
        $incrementSeconds = (int) ($parts[1] ?? 0);
        $totalTime = $baseSeconds + ($incrementSeconds * 40);

        if ($totalTime < 180) {
            return [
                'category' => 'bullet',
                'rating' => $user->bullet_rating ?? 1500,
                'rd' => $user->bullet_rd ?? 350,
                'vol' => 0.06,
            ];
        } elseif ($totalTime < 600) {
            return [
                'category' => 'blitz',
                'rating' => $user->blitz_rating ?? 1500,
                'rd' => $user->blitz_rd ?? 350,
                'vol' => 0.06,
            ];
        } else {
            return [
                'category' => 'rapid',
                'rating' => $user->rapid_rating ?? 1500,
                'rd' => $user->rapid_rd ?? 350,
                'vol' => 0.06,
            ];
        }
    }

    /**
     * Internal API for the chess microservice to report a completed game.
     */
    public function completeGameInternal(Request $request, string $gameId): JsonResponse
    {
        // Security check
        $secret = $request->header('X-Internal-Secret');
        if ($secret !== config('services.chess.internal_secret')) {
            \Illuminate\Support\Facades\Log::warning('Unauthorized internal request!', [
                'game_id' => $gameId,
                'header' => $secret,
                'ip' => $request->ip()
            ]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        \Illuminate\Support\Facades\Log::info('Received game completion report', [
            'game_id' => $gameId,
            'payload' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:completed,aborted',
            'result' => 'nullable|string',
            'termination' => 'nullable|string',
            'rating_changes' => 'nullable|array',
            'rating_changes.white' => 'integer',
            'rating_changes.black' => 'integer',
            'new_ratings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }

        $game = Game::with(['whitePlayer', 'blackPlayer'])->find($gameId);
        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        if ($game->status !== 'active') {
             return response()->json(['message' => 'Game already finalized'], 409);
        }

        DB::transaction(function () use ($request, $game) {
            $status = $request->input('status');
            $ratingChanges = $request->input('rating_changes');
            $newRatings = $request->input('new_ratings');

            $game->update([
                'status' => $status,
                'result' => $request->input('result'),
                'termination' => $request->input('termination'),
                'white_rating_change' => $ratingChanges['white'] ?? null,
                'black_rating_change' => $ratingChanges['black'] ?? null,
            ]);

            // Update user ratings if it was a ranked game (status=completed)
            if ($status === 'completed' && $ratingChanges && $newRatings) {
                $category = $this->getRatingData($game->whitePlayer, $game->time_control)['category'];
                
                // Update White Player
                $whiteUser = $game->whitePlayer;
                $whiteRatingColumn = "{$category}_rating";
                $whiteRdColumn = "{$category}_rd";
                $whiteGamesColumn = "{$category}_games";
                
                \Illuminate\Support\Facades\Log::info('Updating ratings', [
                    'game_id' => $game->id,
                    'category' => $category,
                    'white_change' => $newRatings['white']['rating'] - ($whiteUser->$whiteRatingColumn ?? 1500),
                    'black_change' => $newRatings['black']['rating'] - ($blackUser->$whiteRatingColumn ?? 1500)
                ]);

                $whiteUser->update([
                    $whiteRatingColumn => $newRatings['white']['rating'],
                    $whiteRdColumn => $newRatings['white']['rd'],
                    $whiteGamesColumn => ($whiteUser->$whiteGamesColumn ?? 0) + 1,
                    'last_game_at' => now(),
                ]);

                // Update Black Player
                $blackUser = $game->blackPlayer;
                $blackRatingColumn = "{$category}_rating";
                $blackRdColumn = "{$category}_rd";
                $blackGamesColumn = "{$category}_games";
                
                $blackUser->update([
                    $blackRatingColumn => $newRatings['black']['rating'],
                    $blackRdColumn => $newRatings['black']['rd'],
                    $blackGamesColumn => ($blackUser->$blackGamesColumn ?? 0) + 1,
                    'last_game_at' => now(),
                ]);
            }

            \Illuminate\Support\Facades\Log::info('Game finalized successfully in Laravel', ['game_id' => $game->id]);

            try {
                broadcast(new \App\Events\GameEnded($game));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('GameEnded (internal) broadcast failed: ' . $e->getMessage());
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Internal endpoint for the microservice to create a new game (rematch).
     */
    public function createGameInternal(Request $request)
    {
        // 1. Verify internal secret
        $secret = $request->header('X-Internal-Secret');
        if ($secret !== config('services.chess.internal_secret')) {
            return response()->json(['message' => 'Unauthorized internal request'], 401);
        }

        $validated = $request->validate([
            'white_id' => 'required|exists:users,id',
            'black_id' => 'required|exists:users,id',
            'time_control' => 'required|string',
        ]);

        $whiteId = $validated['white_id'];
        $blackId = $validated['black_id'];
        $timeControl = $validated['time_control'];

        $whitePlayer = \App\Models\User::find($whiteId);
        $blackPlayer = \App\Models\User::find($blackId);

        $timeData = $this->parseTimeControl($timeControl);
        $whiteRatingData = $this->getRatingData($whitePlayer, $timeControl);
        $blackRatingData = $this->getRatingData($blackPlayer, $timeControl);

        $game = \App\Models\Game::create([
            'white_player_id' => $whiteId,
            'black_player_id' => $blackId,
            'status' => 'active',
            'time_control' => $timeControl,
            'initial_time_ms' => $timeData['initial_time_ms'],
            'increment_ms' => $timeData['increment_ms'],
            'white_elo' => $whiteRatingData['rating'],
            'black_elo' => $blackRatingData['rating'],
            'white_rd' => $whiteRatingData['rd'],
            'black_rd' => $blackRatingData['rd'],
            'white_vol' => $whiteRatingData['vol'],
            'black_vol' => $blackRatingData['vol'],
            'white_last_heartbeat_at' => now(),
            'black_last_heartbeat_at' => now(),
        ]);

        // Notify microservice to initialize the game state
        $created = $this->callMicroserviceWithRetry('/api/create-game', [
            'gameId' => $game->id,
            'whitePlayer' => [
                'userId' => $whiteId,
                'socketId' => '', 
                'name' => $whitePlayer->name,
                'rating' => $whiteRatingData['rating'],
                'rd' => $whiteRatingData['rd'],
                'vol' => $whiteRatingData['vol']
            ],
            'blackPlayer' => [
                'userId' => $blackId,
                'socketId' => '', 
                'name' => $blackPlayer->name,
                'rating' => $blackRatingData['rating'],
                'rd' => $blackRatingData['rd'],
                'vol' => $blackRatingData['vol']
            ],
            'timeControl' => $timeControl,
            'initialTimeMs' => $timeData['initial_time_ms'],
            'incrementMs' => $timeData['increment_ms']
        ], 'POST');

        if (!$created) {
            $game->delete();
            return response()->json(['message' => 'Failed to initialize game in microservice'], 503);
        }

        // Broadcast MatchFound to both players
        try {
            broadcast(new \App\Events\GameMatched($game));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Rematch GameMatched broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'game_id' => $game->id,
            'message' => 'Rematch game created'
        ]);
    }
}
