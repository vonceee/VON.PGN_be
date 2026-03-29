<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MovePlayed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Game $game,
        public string $moveUci,
        public string $san,
        public string $newFen,
        public array $clockData,
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
            'move' => $this->moveUci,
            'san' => $this->san,
            'fen' => $this->newFen,
            'turn' => $this->game->turn,
            'white_time_remaining_ms' => $this->clockData['white_time_remaining_ms'],
            'black_time_remaining_ms' => $this->clockData['black_time_remaining_ms'],
            'server_timestamp' => $this->clockData['server_timestamp'],
            'status' => $this->game->status,
            'result' => $this->game->result,
            'termination' => $this->game->termination,
            'is_check' => $this->clockData['is_check'] ?? false,
            'is_checkmate' => $this->clockData['is_checkmate'] ?? false,
            'is_stalemate' => $this->clockData['is_stalemate'] ?? false,
            'is_draw' => $this->clockData['is_draw'] ?? false,
            'legal_moves' => $this->clockData['legal_moves'] ?? [],
        ];
    }
}
