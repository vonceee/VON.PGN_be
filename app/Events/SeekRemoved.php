<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeekRemoved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $seekId,
        public string $reason,
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
            'seek_id' => $this->seekId,
            'reason' => $this->reason,
        ];
    }
}