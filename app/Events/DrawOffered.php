<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DrawOffered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Game $game,
        public int $offeredByUserId,
        public string $offeredByColor,
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
            'offered_by' => $this->offeredByColor,
            'offered_by_user_id' => $this->offeredByUserId,
            'cooldown_expires_at' => $this->game->draw_offered_at
                ? $this->game->draw_offered_at->addSeconds(30)->toIso8601String()
                : null,
        ];
    }
}
