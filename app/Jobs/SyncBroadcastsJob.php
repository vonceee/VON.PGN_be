<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Broadcast;
use App\Models\BroadcastRound;

class SyncBroadcastsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function handle(): void
    {
        try {
            $response = Http::get('https://lichess.org/api/tournament', [
                'status' => 'started,finished',
                'withTeam' => 'true',
            ]);

            if (!$response->successful()) {
                Log::warning('Failed to fetch broadcasts from lichess');
                return;
            }

            $tournaments = $response->json();

            foreach ($tournaments as $tournament) {
                $this->syncTournament($tournament);
            }

            Log::info('Broadcasts synced successfully');
        } catch (\Exception $e) {
            Log::error('Error syncing broadcasts: ' . $e->getMessage());
            throw $e;
        }
    }

    private function syncTournament(array $tournament): void
    {
        $broadcast = Broadcast::updateOrCreate(
            ['lichess_id' => $tournament['id']],
            [
                'name' => $tournament['name'],
                'status' => $tournament['status'],
                'starts_at' => isset($tournament['startsAt']) 
                    ? \Carbon\Carbon::createFromTimestampMs($tournament['startsAt']) 
                    : null,
                'finishes_at' => isset($tournament['finishesAt']) 
                    ? \Carbon\Carbon::createFromTimestampMs($tournament['finishesAt']) 
                    : null,
                'clock' => $tournament['clock'] ?? null,
                'variant' => $tournament['variant'] ?? 'standard',
            ]
        );

        if (isset($tournament['rounds'])) {
            foreach ($tournament['rounds'] as $round) {
                BroadcastRound::updateOrCreate(
                    [
                        'broadcast_id' => $broadcast->id,
                        'lichess_id' => $round['id'],
                    ],
                    [
                        'name' => $round['name'] ?? 'Round ' . $round['number'],
                        'status' => $round['status'] ?? 'created',
                        'starts_at' => isset($round['startsAt']) 
                            ? \Carbon\Carbon::createFromTimestampMs($round['startsAt']) 
                            : null,
                    ]
                );
            }
        }
    }
}