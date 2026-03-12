<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserProfileResource;

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
}
