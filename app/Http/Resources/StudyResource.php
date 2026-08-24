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
            'id' => \App\Utils\StudyObfuscator::encode($this->id),
            'name' => $this->name,
            'visibility' => $this->visibility,
            'category' => $this->category ?? 'general',
            'orientation' => $this->orientation ?? 'white',
            'engine_visibility' => $this->engine_visibility,
            'export_visibility' => $this->export_visibility,
            'description' => $this->description ?? cache()->remember("study_pre_move_comments_{$this->id}", 600, function() {
                $firstChapter = $this->relationLoaded('chapters')
                    ? $this->chapters->sortBy('order')->first()
                    : $this->chapters()->orderBy('order')->first();
                if ($firstChapter && is_array($firstChapter->moves) && count($firstChapter->moves) > 0) {
                    $firstMove = $firstChapter->moves[0] ?? null;
                    if ($firstMove && is_array($firstMove) && !empty($firstMove['preComments'])) {
                        return implode(" ", $firstMove['preComments']);
                    }
                }
                return null;
            }),
            'user_id' => $this->user_id, // ADDED THIS
            'preview_fen' => $this->preview_fen ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
            'preview_last_move' => $this->preview_last_move,
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
