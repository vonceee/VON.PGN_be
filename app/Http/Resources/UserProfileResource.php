<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();

        return [
            // Core User Data
            'uid' => (string) $this->id,
            'email' => $this->email,
            'username' => $this->name,
            'bio' => $this->bio,
            'is_admin' => $this->is_admin,
            'verified_organizer' => $this->verified_organizer ?? false,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'createdAt' => $this->created_at->toIso8601String(),
            'is_online' => $this->is_online ?? false,
            'last_seen_at' => $this->last_seen_at?->toIso8601String(),
            'country_code' => $this->country_code,

            // Follow stats
            'followers_count' => $this->followers_count ?? 0,
            'following_count' => $this->following_count ?? 0,
            'is_following' => $currentUser ? $currentUser->isFollowing($this->resource) : false,



            // Nested Preferences
            'preferences' => [
                'theme' => $this->preferences->theme ?? 'system',
                'boardStyle' => $this->preferences->board_style ?? 'classic',
                'pieceStyle' => $this->preferences->piece_style ?? 'standard',
                'soundEnabled' => (bool) ($this->preferences->sound_enabled ?? true),
            ],

            // Nested Progress & Badges
            'progress' => [
                'completedLessonIds' => $this->progress->completed_lesson_ids ?? [],
                'lastActiveLessonId' => $this->progress->last_active_lesson_id,
                'totalPuzzlesSolved' => $this->progress->total_puzzles_solved ?? 0,
                'currentStreakDays' => $this->progress->current_streak_days ?? 0,
                'puzzleRating' => $this->progress->puzzle_rating ?? 1200,
                'puzzleStreak' => $this->progress->puzzle_streak ?? 0,

                // map over the badges to format them cleanly
                'earnedBadges' => $this->badges->map(function ($badge) {
                    return [
                        'id' => (string) $badge->id,
                        'title' => $badge->name,
                        'description' => $badge->description,
                        'imageUrl' => $badge->image_url,
                        'earnedAt' => $badge->pivot->earned_at,
                    ];
                }),
            ]
        ];
    }


}
