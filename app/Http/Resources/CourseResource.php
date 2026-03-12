<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'chapters' => ChapterResource::collection($this->whenLoaded('chapters')),
        ];
    }
}
