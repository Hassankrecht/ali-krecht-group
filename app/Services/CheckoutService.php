<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Checkout;
use App\Models\CheckoutItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get all checkouts (admin)
     */
    public function getAllCheckouts(int $perPage = 15)
    {
        return Checkout::with(['user', 'items.product', 'coupon'])
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Get user's checkouts
     */
    public function getUserCheckouts(User $user, int $perPage = 15)
    {
        return Checkout::where('user_id', $user->id)
            ->with(['items.product', 'coupon'])
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Validate cart before checkout
     */
    public function validateCart(User $user): array
    {
        return $this->cartService->validateCart($user);
    }

    /**
     * Calculate final checkout totals
     */
    public function calculateCheckoutTotals(
        User $user,
        ?Coupon $coupon = null,
        float $taxRate = 0,
        string $country = null
    ): array {
        return $this->cartService->calculateTotal($user, $coupon, $taxRate, $country);
    }

    /**
     * Create checkout from cart
     */
    public function createCheckout(User $user, array $checkoutData, ?Coupon $coupon = null): Checkout
    {
        // Validate cart first
        $this->validateCart($user);

        // Calculate totals
        $totals = $this->calculateCheckoutTotals($user, $coupon);

        // Get cart items
        $cartItems = $this->cartService->getCartItems($user);

        return DB::transaction(function () use ($user, $checkoutData, $coupon, $totals, $cartItems) {
            // Create checkout record
            $checkout = Checkout::create([
                'user_id' => $user->id,
                'coupon_id' => $coupon?->id,
                'name' => $checkoutData['name'],
                'email' => $checkoutData['email'],
                'phone_number' => $checkoutData['phone_number'],
                'town' => $checkoutData['town'] ?? null,
                'country' => $checkoutData['country'] ?? null,
                'zipcode' => $checkoutData['zipcode'] ?? null,
                'address' => $checkoutData['address'] ?? null,
                'total_price' => $totals['total'],
                'total_before_discount' => $totals['subtotal'],
                'discount_amount' => $totals['discount'],
                'status' => 'pending',
            ]);

            // Create checkout items from cart
            foreach ($cartItems as $cartItem) {
                CheckoutItem::create([
                    'checkout_id' => $checkout->id,
                    'product_id' => $cartItem->product_id,
                    'name' => $cartItem->product->title,
                    'image' => $cartItem->product->images->first()?->image ?? null,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total_price' => $cartItem->quantity * $cartItem->price,
                ]);

                // Update product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Clear cart
            $this->cartService->clearCart($user);

            // Mark coupon as used (if applied)
            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $checkout;
        });
    }

    /**
     * Get checkout details
     */
    public function getCheckout(Checkout $checkout): Checkout
    {
        return $checkout->load(['user', 'items.product', 'coupon']);
    }

    /**
     * Update checkout status
     */
    public function updateStatus(Checkout $checkout, string $status): Checkout
    {
        $validStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            throw new \Exception("Invalid status: {$status}");
        }

        $checkout->update(['status' => $status]);

        return $checkout;
    }

    /**
     * Cancel checkout and restore stock
     */
    public function cancelCheckout(Checkout $checkout): bool
    {
        return DB::transaction(function () use ($checkout) {
            // Restore product stock
            foreach ($checkout->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Restore coupon usage if applied
            if ($checkout->coupon) {
                $checkout->coupon->decrement('used_count');
            }

            // Update status
            $checkout->update(['status' => 'cancelled']);

            return true;
        });
    }

    /**
     * Get checkout summary
     */
    public function getSummary(Checkout $checkout): array
    {
        return [
            'id' => $checkout->id,
            'user_name' => $checkout->user->name ?? $checkout->name,
            'email' => $checkout->email,
            'items_count' => $checkout->items->count(),
            'items_quantity' => $checkout->items->sum('quantity'),
            'subtotal' => $checkout->total_before_discount,
            'discount' => $checkout->discount_amount,
            'total' => $checkout->total_price,
            'status' => $checkout->status,
            'created_at' => $checkout->created_at,
        ];
    }

    /**
     * Get checkout statistics
     */
    public function getStats(): array
    {
        return [
            'total_checkouts' => Checkout::count(),
            'pending' => Checkout::where('status', 'pending')->count(),
            'paid' => Checkout::where('status', 'paid')->count(),
            'shipped' => Checkout::where('status', 'shipped')->count(),
            'delivered' => Checkout::where('status', 'delivered')->count(),
            'cancelled' => Checkout::where('status', 'cancelled')->count(),
            'total_revenue' => Checkout::where('status', 'paid')->sum('total_price'),
        ];
    }

    /**
     * Get recent checkouts
     */
    public function getRecentCheckouts(int $limit = 10): Collection
    {
        return Checkout::with(['user', 'items'])
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search checkouts
     */
    public function search(string $query): Collection
    {
        return Checkout::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone_number', 'like', "%{$query}%");
        })
            ->with(['user', 'items'])
            ->latest('created_at')
            ->get();
    }

    /**
     * Get checkouts by status
     */
    public function getByStatus(string $status, int $perPage = 15)
    {
        return Checkout::where('status', $status)
            ->with(['user', 'items'])
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Get checkouts by date range
     */
    public function getByDateRange($startDate, $endDate, int $perPage = 15)
    {
        return Checkout::whereBetween('created_at', [$startDate, $endDate])
            ->with(['user', 'items'])
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Calculate average order value
     */
    public function getAverageOrderValue(): float
    {
        $total = Checkout::sum('total_price');
        $count = Checkout::count();

        return $count > 0 ? $total / $count : 0;
    }

    /**
     * Export checkouts to array
     */
    public function export(array $filters = []): array
    {
        $query = Checkout::with(['user', 'items']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        return $query->get()->map(function ($checkout) {
            return $this->getSummary($checkout);
        })->toArray();
    }
}
