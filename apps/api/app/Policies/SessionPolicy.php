<?php

namespace App\Policies;

use App\Models\Project\Project;
use App\Models\Session\Session;
use App\Models\User;

class SessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Project $project): bool
    {
        return $project
            ->company
            ->members()
            ->where('member_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Session $session): bool
    {
        return $session
            ->project
            ->company
            ->members()
            ->where('member_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Project $project): bool
    {
        return $project
            ->company
            ->members()
            ->where('member_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Session $sessionSession): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Session $sessionSession): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Session $sessionSession): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Session $sessionSession): bool
    {
        //
    }

    /**
     * Determine whether the user can assign the session to itself
     */
    public function assignSessionToUser(User $loggedUser, Session $session, User $assignTo): bool
    {
        if (! $this->view($loggedUser, $session)) {
            return false;
        }

        return $session
            ->project
            ->company
            ->members()
            ->where('member_id', $assignTo->id)
            ->exists();
    }
}
