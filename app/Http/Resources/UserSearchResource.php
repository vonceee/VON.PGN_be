<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uid' => (string) $this->id,
            'username' => $this->name,
            'bughouse_stats' => [
                'wins' => (int) ($this->bughouse_wins ?? 0),
                'draws' => (int) ($this->bughouse_draws ?? 0),
                'losses' => (int) ($this->bughouse_losses ?? 0),
            ]
        ];
    }
}
