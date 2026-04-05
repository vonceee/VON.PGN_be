<?php

namespace App\Jobs;

use App\Events\GameMatched;
use App\Models\Game;
use App\Models\GameSeek;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameMatchmakingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        private string $timeControl,
    ) {}

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

    public function handle(): void
    {
        // Try to match any remaining pairs
        while (true) {
            $matched = DB::transaction(function () {
                $seek1 = GameSeek::where('time_control', $this->timeControl)
                    ->lockForUpdate()
                    ->oldest()
                    ->first();

                if (!$seek1) {
                    return false;
                }

                $seek2 = GameSeek::where('time_control', $this->timeControl)
                    ->where('user_id', '!=', $seek1->user_id)
                    ->lockForUpdate()
                    ->orderByRaw('ABS(elo - ?)', [$seek1->elo])
                    ->first();

                if (!$seek2) {
                    return false;
                }

                $player1 = $seek1->user;
                $player2 = $seek2->user;

                $seek1->delete();
                $seek2->delete();

                $whiteId = rand(0, 1) ? $player1->id : $player2->id;
                $blackId = $whiteId === $player1->id ? $player2->id : $player1->id;

                $timeData = $this->parseTimeControl($this->timeControl);

                $game = Game::create([
                    'white_player_id' => $whiteId,
                    'black_player_id' => $blackId,
                    'status' => 'active',
                    'time_control' => $this->timeControl,
                    'initial_time_ms' => $timeData['initial_time_ms'],
                    'increment_ms' => $timeData['increment_ms'],
                    'white_elo' => $player1->progress?->puzzle_rating ?? 1200,
                    'black_elo' => $player2->progress?->puzzle_rating ?? 1200,
                ]);

                broadcast(new GameMatched($game));

                Log::info("Game matched: {$game->id} - {$player1->name} vs {$player2->name}");

                return true;
            });

            if (!$matched) {
                break;
            }
        }
    }
}
