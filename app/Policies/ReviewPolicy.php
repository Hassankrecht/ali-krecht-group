<?php

namespace App\Policies;

use App\Models\Admin\Admin;
use App\Models\Review;

class ReviewPolicy
{
    /**
     * Determine whether the admin can view the reviews list.
     */
    public function viewAny(?Admin $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can update (approve/reject) a review.
     */
    public function update(?Admin $user, Review $review): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can delete a review.
     */
    public function delete(?Admin $user, Review $review): bool
    {
        return $user !== null;
    }
}
