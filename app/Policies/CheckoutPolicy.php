<?php

namespace App\Policies;

use App\Models\Checkout;
use App\Models\Admin\Admin;

class CheckoutPolicy
{
    /**
     * Determine whether the admin can view any checkouts.
     */
    public function viewAny(?Admin $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can view the checkout.
     */
    public function view(?Admin $user, Checkout $checkout): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can create checkouts.
     */
    public function create(?Admin $user): bool
    {
        return $user !== null && $user->hasPermission('create-checkouts');
    }

    /**
     * Determine whether the admin can update the checkout.
     */
    public function update(Admin $user, Checkout $checkout): bool
    {
        return $user->hasPermission('update-checkouts');
    }

    /**
     * Determine whether the admin can delete the checkout.
     */
    public function delete(Admin $user, Checkout $checkout): bool
    {
        return $user->hasPermission('delete-checkouts');
    }

    /**
     * Determine whether the admin can restore the checkout.
     */
    public function restore(Admin $user, Checkout $checkout): bool
    {
        return $user->hasPermission('restore-checkouts');
    }

    /**
     * Determine whether the admin can permanently delete the checkout.
     */
    public function forceDelete(Admin $user, Checkout $checkout): bool
    {
        return $user->hasPermission('force-delete-checkouts');
    }
}
