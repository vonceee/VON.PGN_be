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
        $userQuery = User::with(['preferences', 'progress', 'badges']);

        if (is_numeric($id)) {
            $user = $userQuery->find($id);
        } else {
            $user = $userQuery->where('name', $id)->first();
        }

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
}
