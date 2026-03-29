<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameMatched implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Game $game,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->game->white_player_id),
            new PrivateChannel('user.' . $this->game->black_player_id),
        ];
    }

    public function broadcastWith(): array
    {
        $this->game->load(['whitePlayer:id,name', 'blackPlayer:id,name']);

        return [
            'game_id' => $this->game->id,
            'white_player' => [
                'id' => $this->game->whitePlayer->id,
                'name' => $this->game->whitePlayer->name,
            ],
            'black_player' => [
                'id' => $this->game->blackPlayer->id,
                'name' => $this->game->blackPlayer->name,
            ],
            'time_control' => $this->game->time_control,
            'initial_time_ms' => $this->game->initial_time_ms,
            'increment_ms' => $this->game->increment_ms,
            'status' => $this->game->status,
            'initial_fen' => $this->game->initial_fen,
        ];
    }
}
