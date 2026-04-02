<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClockSync implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Game $game,
        public int $whiteTimeMs,
        public int $blackTimeMs,
        public string $serverTimestamp,
        public int $bufferSecondsRemaining = 0,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('game.' . $this->game->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'game_id' => $this->game->id,
            'white_time_remaining_ms' => $this->whiteTimeMs,
            'black_time_remaining_ms' => $this->blackTimeMs,
            'server_timestamp' => $this->serverTimestamp,
            'buffer_seconds_remaining' => $this->bufferSecondsRemaining,
        ];
    }
}
