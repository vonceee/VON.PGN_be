<?php

namespace App\Policies;

use App\Models\Study;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Study $study): bool
    {
        if ($study->visibility === 'public') {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($study->visibility === 'unlisted') {
            return true;
        }

        return $user->id === $study->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Study $study): bool
    {
        return $user->id === $study->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Study $study): bool
    {
        return $user->id === $study->user_id;
    }

    /**
     * Determine whether the user can manage chapters.
     */
    public function manageChapters(User $user, Study $study): bool
    {
        return $user->id === $study->user_id;
    }
}
