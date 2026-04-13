<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArenaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->slug,
            'name' => $this->name,
            'status' => $this->status,
            'start_date' => $this->start_date?->toIso8601String(),
            'end_date' => $this->end_date?->toIso8601String(),
            'timeControl' => $this->time_control,
            'durationMinutes' => $this->duration_minutes,
            'participantsCount' => $this->current_participants,
            'winner' => $this->winner,
            'standings' => $this->standings,
            'createdAt' => $this->created_at?->toIso8601String(),
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'verified_organizer' => $this->creator->verified_organizer ?? false,
                ];
            }),
        ];
    }
}
