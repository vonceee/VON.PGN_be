<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'order' => $this->order,
            // Automatically nest the lessons using the Summary translator!
            'lessons' => LessonSummaryResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
