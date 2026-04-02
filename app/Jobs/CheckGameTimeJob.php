<?php

namespace App\Jobs;

use App\Models\Game;
use App\Services\ClockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckGameTimeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 10;

    public function __construct(
        private string $gameId,
    ) {}

    public function handle(ClockService $clockService): void
    {
        Log::info('[CheckGameTimeJob] Running for game', ['game_id' => $this->gameId]);
        
        $game = Game::find($this->gameId);

        if (!$game || !$game->isActive()) {
            Log::info('[CheckGameTimeJob] Game not active or not found', ['game_id' => $this->gameId, 'exists' => !!$game, 'active' => $game?->isActive()]);
            return;
        }

        // Don't flag during buffer periods
        $now = now();
        $bufferMs = 5000;

        if ($game->turn === 'white' && $game->white_first_move_at === null) {
            if ($game->clock_start_at) {
                $bufferElapsed = max(0, (int) ($now->diffInSeconds($game->clock_start_at, false) * 1000));
                if ($bufferElapsed < $bufferMs) {
                    Log::info('[CheckGameTimeJob] White still in pre-game buffer');
                    return; // Still in buffer
                }
            }
        }

        if ($game->turn === 'black' && $game->black_first_move_at === null && $game->white_first_move_at !== null) {
            $blackFirstMovePossibleAt = $game->white_first_move_at->addSeconds(5);
            if ($now->lt($blackFirstMovePossibleAt)) {
                Log::info('[CheckGameTimeJob] Black still in post-White buffer');
                return; // Still in buffer
            }
        }

        $result = $clockService->checkAndFlag($game);
        Log::info('[CheckGameTimeJob] checkAndFlag result', ['game_id' => $this->gameId, 'result' => $result]);
    }
}
