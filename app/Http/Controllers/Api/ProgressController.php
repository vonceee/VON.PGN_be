<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserProfileResource;
use App\Services\BadgeService;

class ProgressController extends Controller
{
    public function completeLecture(Request $request, BadgeService $badgeService)
    {
        $user = $request->user();
        $progress = $user->progress;

        if ($request->has('lesson_id')) {
            $completed = $progress->completed_lesson_ids ?? [];
            if (!in_array($request->lesson_id, $completed)) {
                $completed[] = $request->lesson_id;
                $progress->completed_lesson_ids = $completed;
                $progress->save();
            }
        }

        // Check if any badges were unlocked
        $newBadges = $badgeService->checkAndAwardBadges($user);

        return response()->json([
            'message' => 'Lecture completed!',
            'new_badges' => $newBadges,
            'user' => new UserProfileResource($user->load(['preferences', 'progress', 'badges']))
        ]);
    }
}