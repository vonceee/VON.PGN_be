<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->slug,
            'name' => $this->name,
            'status' => $this->status,
            'dates' => [
                'start' => $this->start_date?->toDateString(),
                'end' => $this->end_date?->toDateString(),
            ],
            'location' => $this->location,
            'coordinates' => [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ],
            'format' => $this->format,
            'timeControl' => $this->time_control,
            'entryFee' => $this->entry_fee,
            'registrationDeadline' => $this->registration_deadline?->toDateString(),
            'prizePool' => $this->prize_pool,
            'organizer' => $this->organizer,
            'contact' => $this->contact_email,
            'description' => $this->description,
            'registrationInstructions' => $this->registration_instructions,
            'rounds' => $this->rounds,
            'participants' => [
                'current' => $this->current_participants,
                'max' => $this->max_participants,
            ],
            'eligibility' => $this->eligibility,
            'categories' => $this->categories,
            'schedule' => $this->schedule,
            'winner' => $this->winner,
            'standings' => $this->standings,
            'viewCount' => $this->view_count ?? 0,
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
