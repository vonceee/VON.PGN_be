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
        $profilePicture = $this->profile_picture;
        
        // If it's a relative path (not a full URL and not a frontend asset), 
        // transform it to a full storage URL.
        if ($profilePicture && !str_starts_with($profilePicture, 'http') && !str_starts_with($profilePicture, 'assets')) {
            $profilePicture = asset('storage/' . ltrim($profilePicture, '/'));
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'shortInfo' => $this->short_info,
            'fideRating' => $this->fide_rating,
            'profilePicture' => $profilePicture,
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
