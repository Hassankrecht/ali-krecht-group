<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{
    /**
     * Get all approved reviews with pagination
     */
    public function getApprovedReviews(int $perPage = 15): Paginator
    {
        return Review::where('is_approved', true)
            ->latest('created_at')
            ->simplePaginate($perPage);
    }

    /**
     * Get all reviews (admin)
     */
    public function getAllReviews(int $perPage = 15): Paginator
    {
        return Review::latest('created_at')
            ->simplePaginate($perPage);
    }

    /**
     * Get pending reviews (not yet approved)
     */
    public function getPendingReviews(int $perPage = 15): Paginator
    {
        return Review::where('is_approved', false)
            ->latest('created_at')
            ->simplePaginate($perPage);
    }

    /**
     * Create a new review
     */
    public function store(array $data): Review
    {
        // Validate required fields
        $validated = [
            'name' => $data['name'] ?? null,
            'profession' => $data['profession'] ?? null,
            'rating' => (int) ($data['rating'] ?? 5),
            'photo' => $data['photo'] ?? null,
            'review' => $data['review'] ?? null,
            'is_approved' => false, // Reviews require approval by default
        ];

        // Ensure rating is between 1 and 5
        $validated['rating'] = max(1, min(5, $validated['rating']));

        return Review::create($validated);
    }

    /**
     * Update a review
     */
    public function update(Review $review, array $data): Review
    {
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['profession'])) {
            $updateData['profession'] = $data['profession'];
        }

        if (isset($data['rating'])) {
            $updateData['rating'] = max(1, min(5, (int) $data['rating']));
        }

        if (isset($data['review'])) {
            $updateData['review'] = $data['review'];
        }

        if (isset($data['photo'])) {
            $updateData['photo'] = $data['photo'];
        }

        $review->update($updateData);

        return $review;
    }

    /**
     * Approve a review
     */
    public function approve(Review $review): Review
    {
        $review->update(['is_approved' => true]);

        return $review;
    }

    /**
     * Reject (disapprove) a review
     */
    public function reject(Review $review): Review
    {
        $review->update(['is_approved' => false]);

        return $review;
    }

    /**
     * Delete a review
     */
    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    /**
     * Calculate average rating from all approved reviews
     */
    public function calculateAverageRating(): float
    {
        return Review::where('is_approved', true)
            ->avg('rating');
    }

    /**
     * Calculate average rating for a specific product
     * (if product has reviews relationship)
     */
    public function calculateProductRating(Product $product): float
    {
        if (method_exists($product, 'reviews')) {
            return $product->reviews()
                ->where('is_approved', true)
                ->avg('rating');
        }

        return 0;
    }

    /**
     * Get rating distribution (how many reviews for each star)
     */
    public function getRatingDistribution(): array
    {
        $distribution = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        $ratings = Review::where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return array_merge($distribution, $ratings);
    }

    /**
     * Get reviews count by status
     */
    public function getReviewsStats(): array
    {
        return [
            'total' => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending' => Review::where('is_approved', false)->count(),
            'average_rating' => $this->calculateAverageRating(),
        ];
    }

    /**
     * Get recent reviews (latest N)
     */
    public function getRecentReviews(int $limit = 5): Collection
    {
        return Review::where('is_approved', true)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search reviews
     */
    public function search(string $query): Collection
    {
        return Review::where('is_approved', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('review', 'like', "%{$query}%")
                    ->orWhere('profession', 'like', "%{$query}%");
            })
            ->latest('created_at')
            ->get();
    }
}
