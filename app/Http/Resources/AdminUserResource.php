<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'verified_organizer' => $this->verified_organizer,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'last_seen_at' => $this->last_seen_at?->toIso8601String(),
            'is_online' => $this->is_online,
            'ratings' => [
                'bullet' => $this->bullet_rating ?? 1500,
                'blitz' => $this->blitz_rating ?? 1500,
                'rapid' => $this->rapid_rating ?? 1500,
            ],
        ];
    }
}
