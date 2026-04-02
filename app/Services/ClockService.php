<?php

namespace App\Services;

use App\Models\Game;
use App\Events\GameEnded;
use App\Events\ClockSync;
use Illuminate\Support\Facades\Log;

class ClockService
{
    /**
     * Calculate elapsed time and deduct from the current player's clock.
     * Adds increment after the move is applied.
     * Returns the updated time remaining for both players.
     */
    public function applyMoveToClock(Game $game, string $movedPlayerColor): array
    {
        $now = now();
        $elapsedMs = $this->getElapsedMs($game, $now);

        $whiteTime = $game->white_time_remaining_ms;
        $blackTime = $game->black_time_remaining_ms;

        if ($movedPlayerColor === 'white') {
            $whiteTime -= $elapsedMs;
            $whiteTime += $game->increment_ms;
        } else {
            $blackTime -= $elapsedMs;
            $blackTime += $game->increment_ms;
        }

        $whiteTime = max(0, $whiteTime);
        $blackTime = max(0, $blackTime);

        $game->update([
            'white_time_remaining_ms' => $whiteTime,
            'black_time_remaining_ms' => $blackTime,
            'last_move_timestamp' => $now,
        ]);

        return [
            'white_time_remaining_ms' => $whiteTime,
            'black_time_remaining_ms' => $blackTime,
            'server_timestamp' => $now->toISOString(),
        ];
    }

    /**
     * Check if a player's time has expired and end the game if so.
     */
    public function checkAndFlag(Game $game): bool
    {
        if (!$game->isActive()) {
            return false;
        }

        $now = now();
        $elapsedMs = $this->getElapsedMs($game, $now);

        $whiteTime = $game->white_time_remaining_ms;
        $blackTime = $game->black_time_remaining_ms;

        if ($game->turn === 'white') {
            $whiteTime -= $elapsedMs;
        } else {
            $blackTime -= $elapsedMs;
        }

        if ($whiteTime <= 0) {
            $this->flagPlayer($game, 'white');
            return true;
        }

        if ($blackTime <= 0) {
            $this->flagPlayer($game, 'black');
            return true;
        }

        return false;
    }

    /**
     * Flag a player (time expired) and end the game.
     */
    private function flagPlayer(Game $game, string $flaggedColor): void
    {
        $result = $flaggedColor === 'white' ? '0-1' : '1-0';

        $game->update([
            'status' => 'completed',
            'result' => $result,
            'termination' => 'timeout',
            'white_time_remaining_ms' => max(0, $flaggedColor === 'white' ? 0 : $game->white_time_remaining_ms),
            'black_time_remaining_ms' => max(0, $flaggedColor === 'black' ? 0 : $game->black_time_remaining_ms),
        ]);

        broadcast(new GameEnded($game));
    }

    /**
     * Get the current effective time remaining for both players (calculated live).
     */
    public function getEffectiveTimes(Game $game): array
    {
        if (!$game->isActive()) {
            return [
                'white_time_remaining_ms' => $game->white_time_remaining_ms,
                'black_time_remaining_ms' => $game->black_time_remaining_ms,
                'server_timestamp' => now()->toISOString(),
            ];
        }

        // No clock has started yet — return stored times (full time)
        if (!$game->clock_start_at) {
            return [
                'white_time_remaining_ms' => $game->white_time_remaining_ms,
                'black_time_remaining_ms' => $game->black_time_remaining_ms,
                'server_timestamp' => now()->toISOString(),
            ];
        }

        $now = now();
        $elapsedMs = $this->getElapsedMs($game, $now);

        $whiteTime = $game->white_time_remaining_ms;
        $blackTime = $game->black_time_remaining_ms;

        if ($game->turn === 'white') {
            $whiteTime -= $elapsedMs;
        } else {
            $blackTime -= $elapsedMs;
        }

        return [
            'white_time_remaining_ms' => max(0, $whiteTime),
            'black_time_remaining_ms' => max(0, $blackTime),
            'server_timestamp' => $now->toISOString(),
        ];
    }

    /**
     * Calculate elapsed milliseconds since the game clock started running.
     *
     * Timeline:
     *   t=0        Game created (no clock running, clock_start_at = null)
     *   t=0..5     Buffer period (clock frozen, display shows full time)
     *   t=5        Buffer ends, abort timer + clock start ticking
     *   t=5..20    Abort period (clock ticking, game time consumed)
     *   t=m1       First player makes move (last_move_timestamp set to m1,
     *              clock_start_at also set to m1)
     *   t=m1..m1+5 Buffer for second player (clock frozen for second player)
     *   t=m1+5     Second buffer ends (clock ticking for second player)
     *   t=m2       Second player makes move
     *   t=m2+      Normal play (clock always ticking on active turn)
     *
     * Before both players have made their first move, elapsed time is
     * calculated from clock_start_at (which equals the first move time).
     * After both first moves, elapsed is from last_move_timestamp.
     */
    private function getElapsedMs(Game $game, mixed $now): int
    {
        // Before White's first move: clock hasn't started
        if ($game->white_first_move_at === null) {
            return 0;
        }

        // After White's first move but before Black's first move:
        // Clock started at clock_start_at (White's first move time).
        // Elapsed = time since clock_start_at.
        // This correctly shows the game clock ticking during Black's abort period.
        if ($game->black_first_move_at === null) {
            if (!$game->clock_start_at) {
                return 0;
            }
            return max(0, (int) ($now->diffInSeconds($game->clock_start_at, false) * 1000));
        }

        // After both first moves: normal clock behavior from last move timestamp
        if (!$game->last_move_timestamp) {
            return 0;
        }
        return max(0, (int) ($now->diffInSeconds($game->last_move_timestamp, false) * 1000));
    }

    /**
     * Parse a time control string (e.g., "600+5") into initial time and increment in ms.
     */
    public static function parseTimeControl(string $timeControl): array
    {
        $parts = explode('+', $timeControl);
        $baseSeconds = (int) ($parts[0] ?? 600);
        $incrementSeconds = (int) ($parts[1] ?? 0);

        return [
            'initial_time_ms' => $baseSeconds * 1000,
            'increment_ms' => $incrementSeconds * 1000,
        ];
    }
}
