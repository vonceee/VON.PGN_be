<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserSearchResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    public function follow(Request $request, string $userId)
    {
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $currentUser = $request->user();

        if ($currentUser->id === $targetUser->id) {
            return response()->json(['message' => 'You cannot follow yourself'], 422);
        }

        if ($currentUser->isFollowing($targetUser)) {
            return response()->json([
                'message' => 'Already following this user',
                'is_following' => true,
                'followers_count' => $targetUser->followers_count,
            ]);
        }

        DB::transaction(function () use ($currentUser, $targetUser) {
            $currentUser->following()->attach($targetUser->id);
            User::where('id', $currentUser->id)->increment('following_count');
            User::where('id', $targetUser->id)->increment('followers_count');
        });

        return response()->json([
            'message' => 'Followed successfully',
            'is_following' => true,
            'followers_count' => $targetUser->fresh()->followers_count,
        ]);
    }

    public function unfollow(Request $request, string $userId)
    {
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $currentUser = $request->user();

        if ($currentUser->id === $targetUser->id) {
            return response()->json(['message' => 'You cannot unfollow yourself'], 422);
        }

        if (!$currentUser->isFollowing($targetUser)) {
            return response()->json([
                'message' => 'Not following this user',
                'is_following' => false,
                'followers_count' => $targetUser->followers_count,
            ]);
        }

        DB::transaction(function () use ($currentUser, $targetUser) {
            $currentUser->following()->detach($targetUser->id);
            User::where('id', $currentUser->id)->decrement('following_count');
            User::where('id', $targetUser->id)->decrement('followers_count');
        });

        return response()->json([
            'message' => 'Unfollowed successfully',
            'is_following' => false,
            'followers_count' => $targetUser->fresh()->followers_count,
        ]);
    }

    public function followers(Request $request, string $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $search = $request->query('search', '');

        $query = $user->followers();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $followers = $query->orderBy('follows.created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $currentUser = $request->user();
        $data = $followers->items();

        $result = collect($data)->map(function ($follower) use ($currentUser) {
            return [
                'uid' => (string) $follower->id,
                'username' => $follower->name,
                'displayName' => $follower->name,
                'is_following' => $currentUser ? $currentUser->isFollowing($follower) : false,
            ];
        });

        return response()->json([
            'data' => $result,
            'meta' => [
                'current_page' => $followers->currentPage(),
                'last_page' => $followers->lastPage(),
                'per_page' => $followers->perPage(),
                'total' => $followers->total(),
            ],
        ]);
    }

    public function following(Request $request, string $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);
        $search = $request->query('search', '');

        $query = $user->following();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $following = $query->orderBy('follows.created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $data = $following->items();

        $result = collect($data)->map(function ($followedUser) {
            return [
                'uid' => (string) $followedUser->id,
                'username' => $followedUser->name,
                'displayName' => $followedUser->name,
                'is_following' => true,
            ];
        });

        return response()->json([
            'data' => $result,
            'meta' => [
                'current_page' => $following->currentPage(),
                'last_page' => $following->lastPage(),
                'per_page' => $following->perPage(),
                'total' => $following->total(),
            ],
        ]);
    }

    public function status(Request $request, string $userId)
    {
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $currentUser = $request->user();

        return response()->json([
            'is_following' => $currentUser->isFollowing($targetUser),
            'followers_count' => $targetUser->followers_count,
            'following_count' => $targetUser->following_count,
        ]);
    }
}
