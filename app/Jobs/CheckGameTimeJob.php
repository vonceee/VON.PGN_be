<?php

namespace App\Jobs;

use App\Models\Game;
use App\Services\ClockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $game = Game::find($this->gameId);

        if (!$game || !$game->isActive()) {
            return;
        }

        $clockService->checkAndFlag($game);
    }
}
