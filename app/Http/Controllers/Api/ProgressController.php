<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserProfileResource;

class ProgressController extends Controller
{
    public function completeLecture(Request $request)
    {
        // 1. Grab the currently logged-in user and their progress
        $user = $request->user();
        $progress = $user->progress;

        // 2. Add 10 EXP
        $progress->experience_points += 10;

        // 3. Check for Level Up! (Current Level * 100)
        $xpForNextLevel = $progress->current_level * 100;
        $leveledUp = false;

        if ($progress->experience_points >= $xpForNextLevel) {
            $progress->current_level += 1;
            // We keep the rollover EXP! (e.g., 105/100 becomes Level 2 with 5 EXP)
            $progress->experience_points -= $xpForNextLevel;
            $leveledUp = true;
        }

        // 4. (Optional) Save the completed lesson ID if provided
        if ($request->has('lesson_id')) {
            $completed = $progress->completed_lesson_ids ?? [];
            if (!in_array($request->lesson_id, $completed)) {
                $completed[] = $request->lesson_id;
                $progress->completed_lesson_ids = $completed;
            }
        }

        // 5. Save the changes to MySQL
        $progress->save();

        // 6. Return the perfectly updated profile using our Resource!
        return response()->json([
            'message' => 'Lecture completed!',
            'leveled_up' => $leveledUp,
            'gained_xp' => 10,
            // We reload the relations to ensure the data is fresh
            'user' => new UserProfileResource($user->load(['preferences', 'progress', 'badges']))
        ]);
    }
}
