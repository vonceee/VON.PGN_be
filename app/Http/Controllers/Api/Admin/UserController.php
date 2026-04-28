<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'organizer') {
                $query->where('verified_organizer', true);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return AdminUserResource::collection($users);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new AdminUserResource($user);
    }

    public function toggleAdmin($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent removing own admin status
        if (auth()->id() == $user->id) {
            return response()->json(['message' => 'You cannot change your own admin status'], 403);
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return new AdminUserResource($user);
    }

    public function toggleOrganizer($id)
    {
        $user = User::findOrFail($id);
        $user->verified_organizer = !$user->verified_organizer;
        $user->save();

        return new AdminUserResource($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if (auth()->id() == $user->id) {
            return response()->json(['message' => 'You cannot delete yourself'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
