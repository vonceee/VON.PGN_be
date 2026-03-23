<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;

class BadgeService
{
    /**
     * Check if a user has unlocked any new badges.
     * 
     * @param User $user
     * @return array The newly unlocked badges array
     */
    public function checkAndAwardBadges(User $user): array
    {
        $newBadges = [];
        $progress = $user->progress;

        if (!$progress) {
            return $newBadges;
        }

        $allBadges = Badge::all();
        // Load the IDs of badges the user already has
        $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();

        // 1. Level Milestones
        $level = $progress->current_level;

        if ($level >= 2) {
            $badge = $this->awardBadgeIfEligible($user, $allBadges, $earnedBadgeIds, 'Novice Player', 'Reached Level 2');
            if ($badge) $newBadges[] = $badge;
        }

        if ($level >= 5) {
            $badge = $this->awardBadgeIfEligible($user, $allBadges, $earnedBadgeIds, 'Intermediate Player', 'Reached Level 5');
            if ($badge) $newBadges[] = $badge;
        }

        if ($level >= 10) {
            $badge = $this->awardBadgeIfEligible($user, $allBadges, $earnedBadgeIds, 'Advanced Player', 'Reached Level 10');
            if ($badge) $newBadges[] = $badge;
        }

        // 2. Lesson Milestones
        $completedLessonsCount = is_array($progress->completed_lesson_ids) ? count($progress->completed_lesson_ids) : 0;

        if ($completedLessonsCount >= 1) {
            $badge = $this->awardBadgeIfEligible($user, $allBadges, $earnedBadgeIds, 'First Steps', 'Completed your first lesson');
            if ($badge) $newBadges[] = $badge;
        }

        if ($completedLessonsCount >= 5) {
            $badge = $this->awardBadgeIfEligible($user, $allBadges, $earnedBadgeIds, 'Scholar', 'Completed 5 lessons');
            if ($badge) $newBadges[] = $badge;
        }

        if ($completedLessonsCount >= 20) {
            $badge = $this->awardBadgeIfEligible($user, $allBadges, $earnedBadgeIds, 'Grandmaster Scholar', 'Completed 20 lessons');
            if ($badge) $newBadges[] = $badge;
        }

        // 3. Puzzle Milestones (Example hooks for future logic)
        $totalPuzzles = $progress->total_puzzles_solved;
        if ($totalPuzzles >= 10) {
             $badge = $this->awardBadgeIfEligible($user, $allBadges, $earnedBadgeIds, 'Tactician', 'Solved 10 puzzles');
             if ($badge) $newBadges[] = $badge;
        }

        return $newBadges;
    }

    /**
     * Helper to award a badge if the user doesn't already have it.
     */
    private function awardBadgeIfEligible(User $user, $allBadges, $earnedBadgeIds, string $badgeName, string $fallbackDescription = '')
    {
        $badge = $allBadges->where('name', $badgeName)->first();

        // If badge doesn't exist in DB, we can't award it
        if (!$badge) {
            return null;
        }

        if (!in_array($badge->id, $earnedBadgeIds)) {
            // Award the badge
            $user->badges()->attach($badge->id, ['earned_at' => now()]);
            // Return to display on frontend
            return $badge;
        }

        return null; // Already earned
    }
}
