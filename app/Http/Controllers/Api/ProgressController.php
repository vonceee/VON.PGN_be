<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserProfileResource;

class ProgressController extends Controller
{
    public function completeLecture(Request $request)
    {
        $user = $request->user();
        $progress = $user->progress;

        $leveledUp = $progress->gainExperience(10);

        if ($request->has('lesson_id')) {
            $completed = $progress->completed_lesson_ids ?? [];
            if (!in_array($request->lesson_id, $completed)) {
                $completed[] = $request->lesson_id;
                $progress->completed_lesson_ids = $completed;
                $progress->save();
            }
        }

        return response()->json([
            'message' => 'Lecture completed!',
            'leveled_up' => $leveledUp,
            'gained_xp' => 10,
            'user' => new UserProfileResource($user->load(['preferences', 'progress', 'badges']))
        ]);
    }
}