<?php

namespace App\Events;

use App\Models\GameSeek;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeekCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GameSeek $seek,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('seeks'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->seek->id,
            'user_id' => $this->seek->user_id,
            'username' => $this->seek->user?->name,
            'elo' => $this->seek->elo,
            'time_control' => $this->seek->time_control,
            'created_at' => $this->seek->created_at?->toISOString(),
        ];
    }
}