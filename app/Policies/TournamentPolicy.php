<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
{
    public function view(User $user, Tournament $tournament): bool
    {
        return $user->is_admin && $tournament->created_by === $user->id;
    }

    public function update(User $user, Tournament $tournament): bool
    {
        return $user->is_admin && $tournament->created_by === $user->id;
    }

    public function delete(User $user, Tournament $tournament): bool
    {
        return $user->is_admin && $tournament->created_by === $user->id;
    }
}
