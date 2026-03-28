<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserSearchResource;

class UserProfileController extends Controller
{
    public function myProfile(\Illuminate\Http\Request $request)
    {
        $user = User::with(['preferences', 'progress', 'badges'])->find($request->user()->id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new \App\Http\Resources\UserProfileResource($user);
    }

    public function search(\Illuminate\Http\Request $request)
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

        return new \App\Http\Resources\UserProfileResource($user);
    }
}
