<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        /**
         * In this case , projects are public, so any authenticated user can view the list of projects
         */
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        /**
         * In this case , projects are public, so any authenticated or unauthenticaticated user can view a project
         */
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        /**
         * Only super admin and admin can create projects
         */
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        /**
         * Only super admin and admin can update projects
         * Admin can only update their own projects
         */
        return $user->isSuperAdmin() || $project->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        /**
         * Only super admin and admin can delete projects
         * Admin can only delete their own projects
         */
        return $user->isSuperAdmin() || $project->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        /**
         * Super admin can restore any project
         * Admin can only restore their own projects
         */
        return $user->isSuperAdmin() || $project->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        /**
         * Super admin can permanently delete any project
         * Admin can only permanently delete their own projects
         */
        return $user->isSuperAdmin() || $project->user_id === $user->id;
    }
}
