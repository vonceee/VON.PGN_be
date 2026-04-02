<?php

namespace App\Services;

use App\Models\Game;
use App\Events\GameEnded;

class ClockService
{
    /**
     * Lichess-style implementation:
     * - Server stores: last_move_timestamp
     * - Client calculates remaining time locally using: stored_time - (now - last_move_timestamp) + increment
     * - Server only checks timeout when a move is attempted
     */

    /**
     * Apply move to clock - adds increment and subtracts elapsed time.
     * This is the proper Lichess-style: server calculates consumed time.
     */
    public function applyMoveToClock(Game $game, string $movedPlayerColor): array
    {
        $now = now();

        // Validate current times - fallback to initial if corrupted
        $maxValidTime = $game->initial_time_ms * 10;
        
        $whiteTime = $game->white_time_remaining_ms;
        $blackTime = $game->black_time_remaining_ms;
        
        // Fix corrupted values before applying
        if ($whiteTime > $maxValidTime || $whiteTime <= 0) {
            $whiteTime = $game->initial_time_ms;
        }
        if ($blackTime > $maxValidTime || $blackTime <= 0) {
            $blackTime = $game->initial_time_ms;
        }

        // Calculate elapsed time since last move
        $elapsedMs = 0;
        if ($game->last_move_timestamp) {
            $lastTs = strtotime($game->last_move_timestamp);
            $nowTs = strtotime($now);
            $elapsedMs = max(0, ($nowTs - $lastTs) * 1000);
        }

        // Subtract elapsed time from the player who made the move
        if ($movedPlayerColor === 'white') {
            $whiteTime = max(0, $whiteTime - $elapsedMs);
            $whiteTime += $game->increment_ms;
        } else {
            $blackTime = max(0, $blackTime - $elapsedMs);
            $blackTime += $game->increment_ms;
        }

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
     * Check if player's time has expired.
     * Lichess-style: calculate time remaining since last move.
     * Returns true if game was ended.
     */
    public function checkAndFlag(Game $game): bool
    {
        if (!$game->isActive()) {
            return false;
        }

        // Calculate elapsed time since last move (server-side, not from getEffectiveTimes)
        $now = now();
        $elapsedMs = 0;
        
        if ($game->last_move_timestamp) {
            $lastTs = strtotime($game->last_move_timestamp);
            $nowTs = strtotime($now);
            $elapsedMs = max(0, ($nowTs - $lastTs) * 1000);
        }

        // Get the stored time for current player and subtract elapsed
        $storedTime = $game->turn === 'white' 
            ? $game->white_time_remaining_ms 
            : $game->black_time_remaining_ms;
        
        $currentTime = max(0, $storedTime - $elapsedMs);

        if ($currentTime <= 0) {
            $this->flagPlayer($game, $game->turn);
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
     * Get effective times - returns stored time and timestamp for client-side calculation.
     * 
     * Lichess-style: returns the STORED time after last move (with increment added).
     * Client calculates: currentTime = storedTime - (now - lastMoveTimestamp)
     * 
     * Fallback: If stored time is unreasonable (> initial time * 10), use initial time.
     */
    public function getEffectiveTimes(Game $game): array
    {
        $now = now();

        if (!$game->isActive()) {
            return [
                'white_time_remaining_ms' => $game->white_time_remaining_ms,
                'black_time_remaining_ms' => $game->black_time_remaining_ms,
                'server_timestamp' => $now->toISOString(),
            ];
        }

        // If no moves yet, return full initial time
        if (!$game->last_move_timestamp) {
            return [
                'white_time_remaining_ms' => $game->initial_time_ms,
                'black_time_remaining_ms' => $game->initial_time_ms,
                'server_timestamp' => $now->toISOString(),
            ];
        }

        // Validate stored times - fallback to initial if corrupted
        $maxValidTime = $game->initial_time_ms * 10;
        
        $whiteTime = $game->white_time_remaining_ms;
        $blackTime = $game->black_time_remaining_ms;
        
        if ($whiteTime > $maxValidTime || $whiteTime <= 0) {
            $whiteTime = $game->initial_time_ms;
        }
        if ($blackTime > $maxValidTime || $blackTime <= 0) {
            $blackTime = $game->initial_time_ms;
        }

        return [
            'white_time_remaining_ms' => $whiteTime,
            'black_time_remaining_ms' => $blackTime,
            'server_timestamp' => $game->last_move_timestamp->toISOString(),
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