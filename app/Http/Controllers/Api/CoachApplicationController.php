<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoachApplicationResource;
use App\Models\CoachApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoachApplicationController extends Controller
{
    /**
     * Submit a coach profile for listing on the platform
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'shortInfo' => 'nullable|string|max:1000',
            'fideRating' => 'nullable|integer|min:0|max:4000',
            'email' => 'required|email|max:255',
            'playingExperience' => 'nullable|array',
            'playingExperience.*' => 'string|max:255',
            'teachingExperience' => 'nullable|array',
            'teachingExperience.*' => 'string|max:255',
            'teachingMethods' => 'nullable|array',
            'teachingMethods.*' => 'string|max:255',
            'bio' => 'nullable|string|max:5000',
            'location' => 'nullable|string|max:255',
            'availability' => 'nullable|string|max:255',
            'coachingType' => 'nullable|string|max:255',
            'twitter' => 'nullable|url|max:2048',
            'youtube' => 'nullable|url|max:2048',
            'twitch' => 'nullable|url|max:2048',
            'instagram' => 'nullable|url|max:2048',
            'facebook' => 'nullable|url|max:2048',
            'chesscom' => 'nullable|url|max:2048',
            'lichess' => 'nullable|url|max:2048',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        // Handle file upload
        $profilePicturePath = null;
        if ($request->hasFile('profilePicture')) {
            $file = $request->file('profilePicture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $profilePicturePath = $file->storeAs('coach-applications', $filename, 'public');
        }

        // Transform frontend field names to match database
        $data = [
            'name' => $validated['name'],
            'title' => $validated['title'] ?? null,
            'short_info' => $validated['shortInfo'] ?? null,
            'fide_rating' => $validated['fideRating'] ?? null,
            'email' => $validated['email'],
            'playing_experience' => $validated['playingExperience'] ?? [],
            'teaching_experience' => $validated['teachingExperience'] ?? [],
            'teaching_methods' => $validated['teachingMethods'] ?? [],
            'bio' => $validated['bio'] ?? null,
            'location' => $validated['location'] ?? null,
            'availability' => $validated['availability'] ?? null,
            'coaching_type' => $validated['coachingType'] ?? null,
            'twitter' => $validated['twitter'] ?? null,
            'youtube' => $validated['youtube'] ?? null,
            'twitch' => $validated['twitch'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'chesscom' => $validated['chesscom'] ?? null,
            'lichess' => $validated['lichess'] ?? null,
            'profile_picture_path' => $profilePicturePath,
            'submitted_at' => now(),
        ];

        $application = CoachApplication::create($data);

        return (new CoachApplicationResource($application))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Check if the current user has submitted a coach profile
     */
    public function myStatus(Request $request)
    {
        $user = $request->user();
        $application = CoachApplication::where('email', $user->email)->first();

        if ($application) {
            return new CoachApplicationResource($application);
        }

        return response()->json(['has_application' => false]);
    }
}