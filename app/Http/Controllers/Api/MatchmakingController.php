<?php

namespace App\Http\Controllers\Api;

use App\Models\Game;
use App\Models\GameSeek;
use App\Models\User;
use App\Services\ChessMicroservice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class MatchmakingController
{
    private ChessMicroservice $microservice;

    public function __construct(ChessMicroservice $microservice)
    {
        $this->microservice = $microservice;
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

            Log::info('Matchmaking seek called', ['user_id' => $user->id, 'time_control' => $timeControl]);

            // Check if user already has an active game
            $existingGame = Game::with(['whitePlayer:id,name', 'blackPlayer:id,name'])
                ->where('status', 'active')
                ->where(function ($q) use ($user) {
                    $q->where('white_player_id', $user->id)
                      ->orWhere('black_player_id', $user->id);
                })
                ->first();

            if ($existingGame) {
                $gameData = $this->microservice->fetchGameState($existingGame);

                if (!$gameData) {
                    return $this->seek($request);
                }

                return response()->json([
                    'message' => 'You already have an active game',
                    'game_id' => $existingGame->id,
                    'matched' => true,
                    'existing_game' => array_merge($existingGame->toArray(), [
                        'fen' => $gameData['fen'],
                        'turn' => $gameData['turn'],
                        'moves' => $gameData['moves'] ?? [],
                        'white_time_remaining_ms' => $gameData['whiteTimeRemainingMs'],
                        'black_time_remaining_ms' => $gameData['blackTimeRemainingMs'],
                        'server_timestamp' => $gameData['serverTimestamp'] ?? null,
                        'my_color' => $existingGame->getPlayerColor($user->id),
                        'legal_moves' => $gameData['legalMoves'] ?? [],
                        'bufferCountdown' => $gameData['bufferCountdown'] ?? null,
                    ]),
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


            // Immediately try to match within this request
            $matchResult = DB::transaction(function () use ($user, $timeControl, $elo) {
                $match = GameSeek::where('time_control', $timeControl)
                    ->where('user_id', '!=', $user->id)
                    ->lockForUpdate()
                    ->orderByRaw('ABS(elo - ?)', [$elo])
                    ->first();

                if (!$match) return null;

                $opponent = $match->user;
                $matchedSeekId = $match->id;
                $match->delete();

                return ['opponent' => $opponent, 'matchedSeekId' => $matchedSeekId];
            });

            if ($matchResult) {
                return $this->initializeGame($user, $matchResult['opponent'], $timeControl, $matchResult['matchedSeekId']);
            }

            // FALLBACK: Match with a bot immediately if no human found
            $bot = User::where('is_bot', true)->inRandomOrder()->first();
            if ($bot) {
                Log::info("[Matchmaking] Instant bot match for user {$user->id} with bot {$bot->id}");
                $seek->delete();
                return $this->initializeBotGame($user, $bot, $timeControl);
            }

            return response()->json([
                'message' => 'Searching for opponent...',
                'time_control' => $timeControl,
                'matched' => false,
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        } catch (\Throwable $e) {
            Log::error('Matchmaking seek FAILED: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Internal server error'], 500);
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
                }
        
                return response()->json(['message' => 'Removed from queue']);
    }

    /**
     * List all active seeks.
     */
    public function index(): JsonResponse
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
            $seek = GameSeek::find($seekId);

            if (!$seek) return response()->json(['message' => 'Seek not found'], 404);
            if ($seek->user_id === $user->id) return response()->json(['message' => 'Cannot join your own seek'], 400);

            $opponentUser = $seek->user;
            $timeControl = $seek->time_control;
            $seek->delete();


            return $this->initializeGame($user, $opponentUser, $timeControl, $seekId);
        } catch (\Throwable $e) {
            Log::error('joinSeek FAILED: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    /**
     * Internal helper to handle game creation and color randomization.
     */
    private function initializeGame($user, $opponentUser, string $timeControl, ?int $matchedSeekId): JsonResponse
    {
        // Randomize colors
        $whiteId = rand(0, 1) ? $user->id : $opponentUser->id;
        $blackId = $whiteId === $user->id ? $opponentUser->id : $user->id;

        $timeData = $this->parseTimeControl($timeControl);
        $whitePlayer = $whiteId === $user->id ? $user : $opponentUser;
        $blackPlayer = $blackId === $user->id ? $user : $opponentUser;

        $whiteRating = $this->getRatingData($whitePlayer, $timeControl);
        $blackRating = $this->getRatingData($blackPlayer, $timeControl);

        $game = Game::create([
            'white_player_id' => $whiteId,
            'black_player_id' => $blackId,
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
            'whitePlayer' => [
                'userId' => $whiteId,
                'name' => $whitePlayer->name,
                'rating' => $whiteRating['rating'],
                'rd' => $whiteRating['rd'],
                'vol' => $whiteRating['vol']
            ],
            'blackPlayer' => [
                'userId' => $blackId,
                'name' => $blackPlayer->name,
                'rating' => $blackRating['rating'],
                'rd' => $blackRating['rd'],
                'vol' => $blackRating['vol']
            ],
            'timeControl' => $timeControl,
            'initialTimeMs' => $timeData['initial_time_ms'],
            'incrementMs' => $timeData['increment_ms']
        ]);

        if (!$created) {
            $game->delete();
            return response()->json(['message' => 'Chess microservice unavailable'], 503);
        }


        return response()->json([
            'message' => 'Match found!',
            'game_id' => $game->id,
            'matched' => true,
            'game' => $game->load(['whitePlayer:id,name', 'blackPlayer:id,name'])
        ]);
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
        $totalTime = (int)($parts[0] ?? 600) + ((int)($parts[1] ?? 0) * 40);

        if ($totalTime < 180) {
            return ['category' => 'bullet', 'rating' => $user->bullet_rating ?? 1500, 'rd' => $user->bullet_rd ?? 350, 'vol' => 0.06];
        } elseif ($totalTime < 600) {
            return ['category' => 'blitz', 'rating' => $user->blitz_rating ?? 1500, 'rd' => $user->blitz_rd ?? 350, 'vol' => 0.06];
        } else {
            return ['category' => 'rapid', 'rating' => $user->rapid_rating ?? 1500, 'rd' => $user->rapid_rd ?? 350, 'vol' => 0.06];
        }
    }
    private function initializeBotGame($user, $bot, string $timeControl): JsonResponse
    {
        $whiteId = rand(0, 1) ? $user->id : $bot->id;
        $blackId = $whiteId === $user->id ? $bot->id : $user->id;

        $timeData = $this->parseTimeControl($timeControl);
        $whitePlayer = $whiteId === $user->id ? $user : $bot;
        $blackPlayer = $blackId === $user->id ? $user : $bot;

        $whiteRating = $this->getRatingData($whitePlayer, $timeControl);
        $blackRating = $this->getRatingData($blackPlayer, $timeControl);

        $game = Game::create([
            'white_player_id' => $whiteId,
            'black_player_id' => $blackId,
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
            'whitePlayer' => [
                'userId' => $whiteId,
                'name' => $whitePlayer->name,
                'isBot' => $whiteId === $bot->id,
                'rating' => $whiteRating['rating'],
                'rd' => $whiteRating['rd'],
                'vol' => $whiteRating['vol']
            ],
            'blackPlayer' => [
                'userId' => $blackId,
                'name' => $blackPlayer->name,
                'isBot' => $blackId === $bot->id,
                'rating' => $blackRating['rating'],
                'rd' => $blackRating['rd'],
                'vol' => $blackRating['vol']
            ],
            'timeControl' => $timeControl,
            'initialTimeMs' => $timeData['initial_time_ms'],
            'incrementMs' => $timeData['increment_ms']
        ]);

        if (!$created) {
            $game->delete();
            return response()->json(['message' => 'Initialization failed'], 503);
        }

        return response()->json([
            'message' => 'Match found!',
            'game_id' => $game->id,
            'matched' => true,
            'game' => $game->load(['whitePlayer:id,name', 'blackPlayer:id,name'])
        ]);
    }
}
