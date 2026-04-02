<?php

namespace App\Http\Controllers\Api;

use App\Events\GameEnded;
use App\Events\MovePlayed;
use App\Events\ClockSync;
use App\Events\DrawOffered;
use App\Jobs\CheckGameTimeJob;
use App\Models\Game;
use App\Models\GameSeek;
use App\Services\ChessService;
use App\Services\ClockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GameController
{
    public function __construct(
        private ChessService $chessService,
        private ClockService $clockService,
    ) {}

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

            // Upsert this user's seek (in case they re-seek the same time control)
            GameSeek::updateOrCreate(
                ['user_id' => $user->id, 'time_control' => $timeControl],
                ['elo' => $elo, 'created_at' => now()],
            );

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

                $opponent = $match->user;
                $match->delete();

                return $opponent;
            });

            if ($opponent) {
                // Remove own seek too
                GameSeek::where('user_id', $user->id)->where('time_control', $timeControl)->delete();

                // Randomize colors
                $whiteId = rand(0, 1) ? $user->id : $opponent->id;
                $blackId = $whiteId === $user->id ? $opponent->id : $user->id;

                $timeData = ClockService::parseTimeControl($timeControl);

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
                    'black_elo' => $opponent->progress?->puzzle_rating ?? 1200,
                ]);

                \Illuminate\Support\Facades\Log::info('Game created', ['game_id' => $game->id]);

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
        GameSeek::where('user_id', $user->id)
            ->where('time_control', $request->input('time_control'))
            ->delete();

        return response()->json(['message' => 'Removed from queue']);
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

        try {
            $clockTimes = $this->clockService->getEffectiveTimes($game);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('getEffectiveTimes failed: ' . $e->getMessage(), ['game_id' => $gameId]);
            $clockTimes = [
                'white_time_remaining_ms' => $game->white_time_remaining_ms,
                'black_time_remaining_ms' => $game->black_time_remaining_ms,
                'server_timestamp' => now()->toISOString(),
            ];
        }

        $legalMoves = $game->isActive() ? $this->chessService->getLegalMoves($game->current_fen) : [];

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
                'fen' => $game->current_fen,
                'turn' => $game->turn,
                'moves' => $game->moves ?? [],
                'white_time_remaining_ms' => $clockTimes['white_time_remaining_ms'],
                'black_time_remaining_ms' => $clockTimes['black_time_remaining_ms'],
                'server_timestamp' => $clockTimes['server_timestamp'],
                'result' => $game->result,
                'termination' => $game->termination,
                'my_color' => $game->getPlayerColor($user->id),
                'legal_moves' => $legalMoves,
                'draw_offered_by' => $game->draw_offered_by,
                'draw_offered_at' => $game->draw_offered_at?->toIso8601String(),
                'buffer_seconds_remaining' => $clockTimes['buffer_seconds_remaining'] ?? 0,
            ],
        ]);
    }

    /**
     * Play a move.
     */
    public function move(Request $request, string $gameId): JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('[move] ENDPOINT HIT', [
            'gameId' => $gameId,
            'user_id' => $request->user()->id ?? 'not authenticated',
            'headers' => $request->headers->all(),
        ]);

        $validator = Validator::make($request->all(), [
            'move' => 'required|string|regex:/^[a-h][1-8][a-h][1-8][qrnb]?$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid move format. Use UCI notation, e.g. "e2e4".'], 422);
        }

        $game = Game::find($gameId);
        if (!$game) {
            \Illuminate\Support\Facades\Log::warning('[move] Game not found', ['gameId' => $gameId]);
            return response()->json(['message' => 'Game not found'], 404);
        }

        $user = $request->user();

        if (!$game->isActive()) {
            \Illuminate\Support\Facades\Log::warning('[move] Game not active', ['gameId' => $gameId, 'status' => $game->status]);
            return response()->json(['message' => 'Game is not active'], 422);
        }

        if (!$game->isPlayer($user->id)) {
            \Illuminate\Support\Facades\Log::warning('[move] Not a player', ['gameId' => $gameId, 'user_id' => $user->id]);
            return response()->json(['message' => 'You are not a player in this game'], 403);
        }

        $playerColor = $game->getPlayerColor($user->id);
        if ($playerColor !== $game->turn) {
            \Illuminate\Support\Facades\Log::warning('[move] Not your turn', [
                'gameId' => $gameId,
                'playerColor' => $playerColor,
                'gameTurn' => $game->turn,
            ]);
            return response()->json(['message' => 'It is not your turn'], 422);
        }

        $uciMove = $request->input('move');

        \Illuminate\Support\Facades\Log::info('[move] Processing move', [
            'gameId' => $gameId,
            'move' => $uciMove,
            'currentFen' => $game->current_fen,
        ]);

        // Check for timeout before processing move
        if ($this->clockService->checkAndFlag($game)) {
            return response()->json([
                'message' => 'Time expired',
                'game_status' => 'completed',
                'result' => $game->result,
            ], 422);
        }

        // Validate the move with the chess engine
        $result = $this->chessService->validateMove($game->current_fen, $uciMove);

        if ($result === null) {
            \Illuminate\Support\Facades\Log::warning('[move] Illegal move', ['gameId' => $gameId, 'move' => $uciMove, 'fen' => $game->current_fen]);
            return response()->json(['message' => 'Illegal move'], 422);
        }

        \Illuminate\Support\Facades\Log::info('[move] Move validated', ['gameId' => $gameId, 'result' => $result]);

        // Apply clock
        $clockData = $this->clockService->applyMoveToClock($game, $playerColor);

        // Refresh game to get updated time values
        $game->refresh();

        // Determine new turn
        $newTurn = $this->chessService->getTurn($result['fen']);

        // Check game status
        $status = $this->chessService->getGameStatus($result['fen']);
        $gameStatus = 'active';
        $gameResult = null;
        $termination = null;

        $legalMoves = $this->chessService->getLegalMoves($result['fen']);

        switch ($status) {
            case 'checkmate':
                $gameStatus = 'completed';
                $gameResult = $game->turn === 'white' ? '1-0' : '0-1';
                $termination = 'checkmate';
                break;
            case 'stalemate':
                $gameStatus = 'completed';
                $gameResult = '1/2-1/2';
                $termination = 'stalemate';
                break;
            case 'draw':
                $gameStatus = 'completed';
                $gameResult = '1/2-1/2';
                $termination = 'draw';
                break;
        }

        // Update moves array
        $moves = $game->moves ?? [];
        $moves[] = $uciMove;

        $updateData = [
            'current_fen' => $result['fen'],
            'turn' => $newTurn,
            'moves' => $moves,
            'status' => $gameStatus,
            'result' => $gameResult,
            'termination' => $termination,
            'draw_offered_by' => null,
            'draw_offered_at' => null,
        ];

        // Track first-move timestamps for pre-game buffer (not needed for Lichess-style)
        // last_move_timestamp is now handled by ClockService.applyMoveToClock()

        $game->update($updateData);

        $clockData['is_check'] = $status === 'check';
        $clockData['is_checkmate'] = $status === 'checkmate';
        $clockData['is_stalemate'] = $status === 'stalemate';
        $clockData['is_draw'] = $status === 'draw';
        $clockData['legal_moves'] = $legalMoves;

        \Illuminate\Support\Facades\Log::info('[move] Broadcasting MovePlayed event', [
            'gameId' => $game->id,
            'move' => $uciMove,
            'san' => $result['san'],
            'fen' => $result['fen'],
            'turn' => $game->turn,
        ]);

        broadcast(new MovePlayed($game, $uciMove, $result['san'], $result['fen'], $clockData));

        // Lichess-style: No scheduled jobs. Timeout is only checked when a move is attempted.
        // The client calculates time locally using: stored_time - (now - last_move_timestamp) + increment

        if ($gameStatus === 'completed') {
            broadcast(new GameEnded($game));
        }

        return response()->json([
            'move' => $uciMove,
            'san' => $result['san'],
            'fen' => $result['fen'],
            'turn' => $newTurn,
            'status' => $gameStatus,
            'result' => $gameResult,
            'termination' => $termination,
            'clock' => $clockData,
            'legal_moves' => $legalMoves,
        ]);
    }

    /**
     * Resign from a game.
     */
    public function resign(Request $request, string $gameId): JsonResponse
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

        $playerColor = $game->getPlayerColor($user->id);
        $result = $playerColor === 'white' ? '0-1' : '1-0';

        $game->update([
            'status' => 'completed',
            'result' => $result,
            'termination' => 'resignation',
        ]);

        broadcast(new GameEnded($game));

        return response()->json([
            'message' => 'You resigned',
            'result' => $result,
            'termination' => 'resignation',
        ]);
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
     * Offer, accept, or decline a draw.
     */
    public function draw(Request $request, string $gameId): JsonResponse
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

        $action = $request->input('action', 'offer');
        $playerColor = $game->getPlayerColor($user->id);

        if ($action === 'offer') {
            // Cannot offer if there is already a pending offer from the other player
            if ($game->hasActiveDrawOffer() && $game->draw_offered_by !== $user->id) {
                return response()->json(['message' => 'There is already a draw offer pending. Accept or decline it first.'], 422);
            }

            // Enforce 30-second cooldown
            if ($game->isDrawOfferOnCooldown($user->id)) {
                $secondsLeft = (int) now()->diffInSeconds($game->draw_offered_at->addSeconds(30));
                return response()->json([
                    'message' => 'You must wait before offering a draw again.',
                    'cooldown_remaining_seconds' => $secondsLeft,
                ], 429);
            }

            $game->update([
                'draw_offered_by' => $user->id,
                'draw_offered_at' => now(),
            ]);

            broadcast(new DrawOffered($game, $user->id, $playerColor));

            return response()->json([
                'message' => 'Draw offered',
                'offered_by' => $playerColor,
            ]);
        }

        if ($action === 'accept') {
            if (!$game->hasActiveDrawOffer()) {
                return response()->json(['message' => 'No draw offer to accept'], 422);
            }

            // Only the non-offering player can accept
            if ($game->draw_offered_by === $user->id) {
                return response()->json(['message' => 'You cannot accept your own draw offer'], 422);
            }

            $game->update([
                'status' => 'completed',
                'result' => '1/2-1/2',
                'termination' => 'agreement',
                'draw_offered_by' => null,
                'draw_offered_at' => null,
            ]);

            broadcast(new GameEnded($game));

            return response()->json([
                'message' => 'Draw accepted',
                'result' => '1/2-1/2',
            ]);
        }

        if ($action === 'decline') {
            if (!$game->hasActiveDrawOffer()) {
                return response()->json(['message' => 'No draw offer to decline'], 422);
            }

            // Only the non-offering player can decline
            if ($game->draw_offered_by === $user->id) {
                return response()->json(['message' => 'You cannot decline your own draw offer'], 422);
            }

            // Record the decline time for cooldown (keep draw_offered_at for cooldown enforcement)
            $offeredByUserId = $game->draw_offered_by;
            $game->update([
                'draw_offered_by' => null,
                'draw_offered_at' => now(), // Reset cooldown timer from decline moment
            ]);

            return response()->json([
                'message' => 'Draw declined',
                'declined_by' => $playerColor,
            ]);
        }

        return response()->json(['message' => 'Invalid action'], 422);
    }

    /**
     * Request clock synchronization.
     */
    public function syncClock(Request $request, string $gameId): JsonResponse
    {
        $game = Game::find($gameId);
        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        if (!$game->isPlayer($request->user()->id)) {
            return response()->json(['message' => 'Not authorized'], 403);
        }

        // Check for timeout before syncing
        if ($game->isActive()) {
            $timedOut = $this->clockService->checkAndFlag($game);
            
            if ($timedOut) {
                return response()->json([
                    'message' => 'Time expired',
                    'game_status' => 'completed',
                    'result' => $game->result,
                    'termination' => $game->termination,
                    'white_time_remaining_ms' => $game->white_time_remaining_ms,
                    'black_time_remaining_ms' => $game->black_time_remaining_ms,
                    'fen' => $game->current_fen,
                    'buffer_seconds_remaining' => 0,
                ]);
            }
        }

        $times = $this->clockService->getEffectiveTimes($game);

        broadcast(new ClockSync(
            $game,
            $times['white_time_remaining_ms'],
            $times['black_time_remaining_ms'],
            $times['server_timestamp'],
            $times['buffer_seconds_remaining'] ?? 0,
        ));

        return response()->json($times);
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

            try {
                $clockTimes = $this->clockService->getEffectiveTimes($game);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('activeGame getEffectiveTimes failed: ' . $e->getMessage(), [
                    'game_id' => $game->id,
                    'trace' => $e->getTraceAsString(),
                ]);
                $clockTimes = [
                    'white_time_remaining_ms' => $game->white_time_remaining_ms,
                    'black_time_remaining_ms' => $game->black_time_remaining_ms,
                    'server_timestamp' => now()->toISOString(),
                ];
            }

            try {
                $legalMoves = $this->chessService->getLegalMoves($game->current_fen);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('activeGame getLegalMoves failed: ' . $e->getMessage(), [
                    'game_id' => $game->id,
                    'fen' => $game->current_fen,
                    'trace' => $e->getTraceAsString(),
                ]);
                $legalMoves = [];
            }

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
                    'fen' => $game->current_fen,
                    'turn' => $game->turn,
                    'moves' => $game->moves ?? [],
                    'white_time_remaining_ms' => $clockTimes['white_time_remaining_ms'],
                    'black_time_remaining_ms' => $clockTimes['black_time_remaining_ms'],
                    'server_timestamp' => $clockTimes['server_timestamp'],
                    'result' => $game->result,
                    'termination' => $game->termination,
                    'my_color' => $game->getPlayerColor($user->id),
                    'legal_moves' => $legalMoves,
                    'draw_offered_by' => $game->draw_offered_by,
                    'draw_offered_at' => $game->draw_offered_at?->toIso8601String(),
                    'buffer_seconds_remaining' => $clockTimes['buffer_seconds_remaining'] ?? 0,
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
}
