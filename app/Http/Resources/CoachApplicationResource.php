<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CoachApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'short_info' => $this->short_info,
            'fide_rating' => $this->fide_rating,
            'email' => $this->email,
            'playing_experience' => $this->playing_experience,
            'teaching_experience' => $this->teaching_experience,
            'teaching_methods' => $this->teaching_methods,
            'bio' => $this->bio,
            'location' => $this->location,
            'availability' => $this->availability,
            'coaching_type' => $this->coaching_type,
            'twitter' => $this->twitter,
            'youtube' => $this->youtube,
            'twitch' => $this->twitch,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'chesscom' => $this->chesscom,
            'lichess' => $this->lichess,
            'profile_picture_url' => $this->profile_picture_path
                ? asset('storage/' . $this->profile_picture_path)
                : null,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}