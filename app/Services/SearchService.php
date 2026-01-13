<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class SearchService
{
    /**
     * Search products by keyword
     */
    public function search(string $query, int $perPage = 15): Paginator
    {
        return Product::where(function (Builder $q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhereHas('translations', function (Builder $q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
        })
            ->with(['category', 'images', 'translations'])
            ->latest('created_at')
            ->simplePaginate($perPage);
    }

    /**
     * Filter products by multiple criteria
     */
    public function filter(array $criteria = [], int $perPage = 15): Paginator
    {
        $query = Product::query();

        // Filter by category
        if (isset($criteria['category_id'])) {
            $query->where('category_id', $criteria['category_id']);
        }

        // Filter by price range
        if (isset($criteria['min_price'])) {
            $query->where('price', '>=', $criteria['min_price']);
        }
        if (isset($criteria['max_price'])) {
            $query->where('price', '<=', $criteria['max_price']);
        }

        // Filter by stock availability
        if (isset($criteria['in_stock']) && $criteria['in_stock']) {
            $query->where('stock', '>', 0);
        }

        // Filter by rating
        if (isset($criteria['min_rating'])) {
            $query->whereHas('reviews', function (Builder $q) use ($criteria) {
                $q->where('is_approved', true)
                    ->havingRaw('AVG(rating) >= ?', [$criteria['min_rating']])
                    ->groupBy('product_id');
            });
        }

        // Text search
        if (isset($criteria['search'])) {
            $query->where(function (Builder $q) use ($criteria) {
                $q->where('title', 'like', "%{$criteria['search']}%")
                    ->orWhere('description', 'like', "%{$criteria['search']}%");
            });
        }

        return $query->with(['category', 'images', 'translations'])
            ->simplePaginate($perPage);
    }

    /**
     * Sort products by various criteria
     */
    public function sort(Builder $query, string $sortBy = 'latest'): Builder
    {
        return match ($sortBy) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'popular' => $query->orderBy('views', 'desc'),
            'rating' => $query->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
                ->selectRaw('products.*, AVG(reviews.rating) as avg_rating')
                ->groupBy('products.id')
                ->orderBy('avg_rating', 'desc'),
            'newest' => $query->latest('created_at'),
            'oldest' => $query->oldest('created_at'),
            'best_selling' => $query->orderBy('sold_count', 'desc'),
            default => $query->latest('created_at'),
        };
    }

    /**
     * Advanced search with filters and sorting
     */
    public function advancedSearch(array $criteria = [], string $sortBy = 'latest', int $perPage = 15): Paginator
    {
        $query = Product::query();

        // Apply filters
        if (isset($criteria['category_id'])) {
            $query->where('category_id', $criteria['category_id']);
        }

        if (isset($criteria['min_price'])) {
            $query->where('price', '>=', $criteria['min_price']);
        }

        if (isset($criteria['max_price'])) {
            $query->where('price', '<=', $criteria['max_price']);
        }

        if (isset($criteria['in_stock']) && $criteria['in_stock']) {
            $query->where('stock', '>', 0);
        }

        if (isset($criteria['search'])) {
            $query->where(function (Builder $q) use ($criteria) {
                $q->where('title', 'like', "%{$criteria['search']}%")
                    ->orWhere('description', 'like', "%{$criteria['search']}%");
            });
        }

        // Apply sorting
        $query = $this->sort($query, $sortBy);

        return $query->with(['category', 'images', 'translations'])
            ->simplePaginate($perPage);
    }

    /**
     * Get related products
     */
    public function getRelated(Product $product, int $limit = 6): Collection
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'images'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured products
     */
    public function getFeatured(int $limit = 12): Collection
    {
        return Product::where('is_featured', true)
            ->with(['category', 'images'])
            ->limit($limit)
            ->get();
    }

    /**
     * Search by exact phrase
     */
    public function searchExact(string $phrase, int $perPage = 15): Paginator
    {
        return Product::where('title', $phrase)
            ->orWhere('description', $phrase)
            ->with(['category', 'images'])
            ->simplePaginate($perPage);
    }

    /**
     * Autocomplete search for suggestions
     */
    public function autocomplete(string $query, int $limit = 10): Collection
    {
        return Product::where('title', 'like', "%{$query}%")
            ->select('id', 'title')
            ->limit($limit)
            ->get();
    }

    /**
     * Get trending products
     */
    public function getTrending(int $limit = 10): Collection
    {
        return Product::orderBy('views', 'desc')
            ->with(['category', 'images'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get best sellers
     */
    public function getBestSellers(int $limit = 10): Collection
    {
        return Product::orderBy('sold_count', 'desc')
            ->with(['category', 'images'])
            ->limit($limit)
            ->get();
    }
}
