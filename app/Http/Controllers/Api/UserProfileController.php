<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserSearchResource;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function myProfile(Request $request)
    {
        $user = User::with(['preferences', 'progress', 'badges'])->find($request->user()->id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new UserProfileResource($user);
    }

    public function updateBio(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $user->update([
            'bio' => $request->input('bio', ''),
        ]);

        $user->load(['preferences', 'progress', 'badges']);

        return new UserProfileResource($user);
    }

    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query || strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $users = User::where('name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->limit(10)
            ->get();

        return UserSearchResource::collection($users);
    }

    public function showProfile(string $id)
    {
        $user = User::with(['preferences', 'progress', 'badges'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new UserProfileResource($user);
    }

    public function toggleVerifiedOrganizer(string $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'verified_organizer' => !$user->verified_organizer,
        ]);

        return response()->json([
            'message' => $user->verified_organizer ? 'User verified as organizer' : 'Verification removed',
            'verified_organizer' => $user->verified_organizer,
        ]);
    }

    public function updateRating(Request $request)
    {
        $request->validate([
            'category' => 'required|in:bullet,blitz,rapid',
            'rating' => 'required|integer|min:100|max:3500',
            'rd' => 'required|integer|min:30|max:350',
        ]);

        $user = $request->user();
        $category = $request->input('category');
        $rating = $request->input('rating');
        $rd = $request->input('rd');

        $gamesColumn = $category . '_games';
        $ratingColumn = $category . '_rating';
        $rdColumn = $category . '_rd';

        $user->update([
            $ratingColumn => $rating,
            $rdColumn => $rd,
            $gamesColumn => ($user->$gamesColumn ?? 0) + 1,
            'last_game_at' => now(),
        ]);

        $user->load(['preferences', 'progress', 'badges']);

        return new UserProfileResource($user);
    }
}
