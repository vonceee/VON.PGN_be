<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $request->user('sanctum');
        $completedIds = $user ? ($user->progress->completed_lesson_ids ?? []) : [];

        return [
            'id' => $this->slug,
            'title' => $this->title,
            'isCompleted' => in_array($this->slug, $completedIds),

            'contentBlocks' => $this->content_blocks,
        ];
    }
}
