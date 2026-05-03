<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Synthesizing a root node for Lichess compatibility
        $root = [
            'id' => '',
            'ply' => 0,
            'fen' => $this->initial_fen,
            'children' => $this->moves ?? []
        ];

        return [
            'id' => (string)$this->id,
            'study_id' => (string)$this->study_id,
            'name' => $this->name,
            'initial_fen' => $this->initial_fen,
            'current_fen' => $this->current_fen,
            'orientation' => $this->orientation ?? 'white',
            'moves' => $this->moves ?? [],
            'treeParts' => [$root], // Lichess expects an array where the first item is root
            'pgn_tags' => $this->pgn_tags ?? [],
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
