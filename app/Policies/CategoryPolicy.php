<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Admin\Admin;

class CategoryPolicy
{
    /**
     * Determine whether the admin can view any categories.
     */
    public function viewAny(?Admin $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can view the category.
     */
    public function view(?Admin $user, Category $category): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can create categories.
     */
    public function create(?Admin $user): bool
    {
        return $user !== null && $user->hasPermission('create-categories');
    }

    /**
     * Determine whether the admin can update the category.
     */
    public function update(Admin $user, Category $category): bool
    {
        return $user->hasPermission('update-categories');
    }

    /**
     * Determine whether the admin can delete the category.
     */
    public function delete(Admin $user, Category $category): bool
    {
        return $user->hasPermission('delete-categories');
    }

    /**
     * Determine whether the admin can restore the category.
     */
    public function restore(Admin $user, Category $category): bool
    {
        return $user->hasPermission('restore-categories');
    }

    /**
     * Determine whether the admin can permanently delete the category.
     */
    public function forceDelete(Admin $user, Category $category): bool
    {
        return $user->hasPermission('force-delete-categories');
    }
}
