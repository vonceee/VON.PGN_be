<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonSummaryResource extends JsonResource
{
    public function toArray($request)
    {
        // Safely check if the user is logged in and grab their completed IDs array
        $user = $request->user('sanctum');
        $completedIds = $user ? ($user->progress->completed_lesson_ids ?? []) : [];

        return [
            'id' => $this->slug, // Frontend expects a string ID!
            'title' => $this->title,
            'isCompleted' => in_array($this->slug, $completedIds),
        ];
    }
}
