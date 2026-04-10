<?php

namespace App\Jobs;

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
     * Get the rating category and values for a user based on time control.
     */
    private function getRatingData($user): array
    {
        $parts = explode('+', $this->timeControl);
        $baseSeconds = (int) ($parts[0] ?? 600);
        $incrementSeconds = (int) ($parts[1] ?? 0);
        $totalTime = $baseSeconds + ($incrementSeconds * 40); // Standard heuristic

        if ($totalTime < 180) {
            return [
                'rating' => $user->bullet_rating ?? 1500,
                'rd' => $user->bullet_rd ?? 350,
                'vol' => 0.06, // Default volatility for new users
            ];
        } elseif ($totalTime < 600) {
            return [
                'rating' => $user->blitz_rating ?? 1500,
                'rd' => $user->blitz_rd ?? 350,
                'vol' => 0.06,
            ];
        } else {
            return [
                'rating' => $user->rapid_rating ?? 1500,
                'rd' => $user->rapid_rd ?? 350,
                'vol' => 0.06,
            ];
        }
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
                
                $whitePlayer = $whiteId === $player1->id ? $player1 : $player2;
                $blackPlayer = $blackId === $player1->id ? $player1 : $player2;

                $whiteRatingData = $this->getRatingData($whitePlayer);
                $blackRatingData = $this->getRatingData($blackPlayer);

                $timeData = $this->parseTimeControl($this->timeControl);

                $game = Game::create([
                    'white_player_id' => $whiteId,
                    'black_player_id' => $blackId,
                    'status' => 'active',
                    'time_control' => $this->timeControl,
                    'initial_time_ms' => $timeData['initial_time_ms'],
                    'increment_ms' => $timeData['increment_ms'],
                    'white_elo' => $whiteRatingData['rating'],
                    'black_elo' => $blackRatingData['rating'],
                    'white_rd' => $whiteRatingData['rd'],
                    'black_rd' => $blackRatingData['rd'],
                    'white_vol' => $whiteRatingData['vol'],
                    'black_vol' => $blackRatingData['vol'],
                ]);


                Log::info("Game matched: {$game->id} - {$player1->name} vs {$player2->name}");

                return true;
            });

            if (!$matched) {
                break;
            }
        }
    }
}
