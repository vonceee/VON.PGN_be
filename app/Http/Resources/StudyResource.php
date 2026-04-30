<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'visibility' => $this->visibility,
            'user_id' => $this->user_id, // ADDED THIS
            'owner' => $this->whenLoaded('owner', function() {
                return [
                    'id' => $this->owner->id,
                    'name' => $this->owner->name,
                ];
            }),
            'chapters_count' => $this->chapters_count ?? ($this->relationLoaded('chapters') ? $this->chapters->count() : $this->chapters()->count()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'chapters' => StudyChapterResource::collection($this->whenLoaded('chapters')),
            'collaborators' => $this->whenLoaded('collaborators', function() {
                return $this->collaborators->map(function($user) {
                    $userData = (new UserProfileResource($user))->toArray(request());
                    return array_merge($userData, [
                        'can_edit' => (bool) ($user->pivot->can_edit ?? true),
                        'is_syncing' => (bool) ($user->pivot->is_syncing ?? true),
                    ]);
                });
            }),
        ];
    }
}
