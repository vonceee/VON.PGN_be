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
        $elapsedMs = 0;

        if ($game->last_move_timestamp) {
            $elapsedMs = max(0, (int) ($now->diffInSeconds($game->last_move_timestamp, false) * 1000));
        }

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
        $elapsedMs = 0;

        if ($game->last_move_timestamp) {
            $elapsedMs = max(0, (int) ($now->diffInSeconds($game->last_move_timestamp, false) * 1000));
        }

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
        if (!$game->last_move_timestamp || !$game->isActive()) {
            return [
                'white_time_remaining_ms' => $game->white_time_remaining_ms,
                'black_time_remaining_ms' => $game->black_time_remaining_ms,
                'server_timestamp' => now()->toISOString(),
            ];
        }

        $now = now();
        $elapsedMs = max(0, (int) ($now->diffInSeconds($game->last_move_timestamp, false) * 1000));

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
