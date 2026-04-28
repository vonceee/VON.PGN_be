<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoachResource extends JsonResource
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
            'title' => $this->title,
            'shortInfo' => $this->short_info,
            'fideRating' => $this->fide_rating,
            'profilePicture' => $this->profile_picture ? (str_starts_with($this->profile_picture, 'http') ? $this->profile_picture : \Illuminate\Support\Facades\Storage::disk('public')->url($this->profile_picture)) : null,
            'isAcademyInstructor' => $this->is_academy_instructor,
            'playingExperience' => $this->playing_experience,
            'teachingExperience' => $this->teaching_experience,
            'bio' => $this->bio,
            'location' => $this->location,
            'availability' => $this->availability,
            'teachingMethods' => $this->teaching_methods,
            'coachingType' => $this->coaching_type,
            'socialMedia' => $this->social_media,
        ];
    }
}
