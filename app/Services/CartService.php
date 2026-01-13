<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Get user's cart items
     */
    public function getCartItems(User $user): Collection
    {
        return Cart::where('user_id', $user->id)
            ->with('product')
            ->get();
    }

    /**
     * Add product to cart
     */
    public function addItem(User $user, Product $product, int $quantity = 1): Cart
    {
        // Check if product already in cart
        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Increase quantity
            $cartItem->increment('quantity', $quantity);

            return $cartItem;
        }

        // Create new cart item
        return Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price, // Store price at time of adding
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Cart $item, int $quantity): Cart
    {
        if ($quantity <= 0) {
            $this->removeItem($item);

            return $item;
        }

        // Check product stock
        if ($quantity > $item->product->stock) {
            throw new \Exception("Not enough stock. Available: {$item->product->stock}");
        }

        $item->update(['quantity' => $quantity]);

        return $item;
    }

    /**
     * Remove item from cart
     */
    public function removeItem(Cart $item): bool
    {
        return $item->delete();
    }

    /**
     * Remove all items from cart
     */
    public function clearCart(User $user): bool
    {
        return Cart::where('user_id', $user->id)->delete();
    }

    /**
     * Calculate cart subtotal (before discount)
     */
    public function calculateSubtotal(User $user): float
    {
        return Cart::where('user_id', $user->id)
            ->sum(DB::raw('quantity * price'));
    }

    /**
     * Calculate tax (if applicable)
     */
    public function calculateTax(User $user, float $taxRate = 0): float
    {
        $subtotal = $this->calculateSubtotal($user);

        return $subtotal * $taxRate / 100;
    }

    /**
     * Calculate shipping cost (can be customized per country)
     */
    public function calculateShipping(User $user, string $country = null): float
    {
        // Simple example - can be enhanced with actual shipping rules
        $itemCount = Cart::where('user_id', $user->id)->sum('quantity');

        if ($itemCount === 0) {
            return 0;
        }

        // Free shipping for orders over 500
        $subtotal = $this->calculateSubtotal($user);
        if ($subtotal >= 500) {
            return 0;
        }

        // Base shipping cost
        return 30;
    }

    /**
     * Apply coupon to cart (returns discount amount)
     */
    public function applyCoupon(User $user, ?Coupon $coupon): float
    {
        if (!$coupon) {
            return 0;
        }

        // Check if coupon is valid
        if (!$coupon->is_active || ($coupon->expires_at && $coupon->expires_at->isPast())) {
            throw new \Exception('This coupon has expired or is not active');
        }

        // Check if coupon has stock
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            throw new \Exception('This coupon has reached its usage limit');
        }

        $subtotal = $this->calculateSubtotal($user);

        // Calculate discount
        if ($coupon->discount_type === 'percentage') {
            $discount = $subtotal * ($coupon->discount_value / 100);
        } else {
            $discount = $coupon->discount_value;
        }

        // Ensure discount doesn't exceed subtotal
        return min($discount, $subtotal);
    }

    /**
     * Calculate total with all adjustments
     */
    public function calculateTotal(
        User $user,
        ?Coupon $coupon = null,
        float $taxRate = 0,
        string $country = null
    ): array {
        $subtotal = $this->calculateSubtotal($user);
        $discount = $this->applyCoupon($user, $coupon);
        $afterDiscount = $subtotal - $discount;
        $tax = $this->calculateTax($user, $taxRate);
        $shipping = $this->calculateShipping($user, $country);
        $total = $afterDiscount + $tax + $shipping;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'after_discount' => $afterDiscount,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
        ];
    }

    /**
     * Get cart item count
     */
    public function getItemCount(User $user): int
    {
        return Cart::where('user_id', $user->id)->sum('quantity');
    }

    /**
     * Get unique product count in cart
     */
    public function getUniqueItemCount(User $user): int
    {
        return Cart::where('user_id', $user->id)->count();
    }

    /**
     * Check if product is in cart
     */
    public function hasProduct(User $user, Product $product): bool
    {
        return Cart::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
    }

    /**
     * Validate cart before checkout
     */
    public function validateCart(User $user): array
    {
        $cartItems = $this->getCartItems($user);

        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        $errors = [];

        foreach ($cartItems as $item) {
            // Check if product still exists
            if (!$item->product) {
                $errors[] = "Product {$item->product_id} has been deleted";

                continue;
            }

            // Check product stock
            if ($item->quantity > $item->product->stock) {
                $errors[] = "{$item->product->title} has insufficient stock. Only {$item->product->stock} available";
            }

            // Check if product is still active
            if (isset($item->product->is_active) && !$item->product->is_active) {
                $errors[] = "{$item->product->title} is no longer available";
            }
        }

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        return [
            'valid' => true,
            'itemCount' => $this->getItemCount($user),
            'itemsCount' => $cartItems->count(),
        ];
    }
}
