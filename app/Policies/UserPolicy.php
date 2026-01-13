<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin\Admin;

class UserPolicy
{
    /**
     * Determine whether the admin can view any users.
     */
    public function viewAny(?Admin $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can view the user.
     */
    public function view(?Admin $user, User $targetUser): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can create users.
     */
    public function create(?Admin $user): bool
    {
        return $user !== null && $user->hasPermission('create-users');
    }

    /**
     * Determine whether the admin can update the user.
     */
    public function update(Admin $user, User $targetUser): bool
    {
        return $user->hasPermission('update-users');
    }

    /**
     * Determine whether the admin can delete the user.
     */
    public function delete(Admin $user, User $targetUser): bool
    {
        return $user->hasPermission('delete-users');
    }

    /**
     * Determine whether the admin can restore the user.
     */
    public function restore(Admin $user, User $targetUser): bool
    {
        return $user->hasPermission('restore-users');
    }

    /**
     * Determine whether the admin can permanently delete the user.
     */
    public function forceDelete(Admin $user, User $targetUser): bool
    {
        return $user->hasPermission('force-delete-users');
    }

    /**
     * Determine whether user can view own profile
     */
    public function viewOwn(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id;
    }

    /**
     * Determine whether user can update own profile
     */
    public function updateOwn(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id;
    }
}
