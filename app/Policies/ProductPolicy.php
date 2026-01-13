<?php

namespace App\Policies;

use App\Models\Admin\Admin;
use App\Models\Product;

class ProductPolicy
{
    /**
     * Determine whether the admin can view any products.
     */
    public function viewAny(?Admin $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can view the product.
     */
    public function view(?Admin $user, Product $product): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can create products.
     */
    public function create(?Admin $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can update the product.
     */
    public function update(?Admin $user, Product $product): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the admin can delete the product.
     */
    public function delete(?Admin $user, Product $product): bool
    {
        return $user !== null;
    }
}
