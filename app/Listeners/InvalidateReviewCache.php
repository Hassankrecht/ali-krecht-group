<?php

namespace App\Listeners;

use App\Events\ReviewApproved;
use Illuminate\Support\Facades\Cache;

class InvalidateReviewCache
{
    /**
     * Handle ReviewApproved event
     */
    public function handle(ReviewApproved $event)
    {
        $this->invalidateCache();
    }

    /**
     * Invalidate all review-related caches
     */
    private function invalidateCache()
    {
        // Get all cache versions to clear
        $cacheVersions = config('cache.versions', ['1']);
        $locales = config('app.supported_locales', ['en', 'ar']);

        foreach ($cacheVersions as $version) {
            foreach ($locales as $locale) {
                // Clear review-related caches
                Cache::forget("home.reviews.{$version}.{$locale}");
                Cache::forget("reviews.stats");
                Cache::forget("reviews.average_rating");
                Cache::forget("reviews.distribution");
            }
        }

        // Clear all review-related tags
        Cache::tags(['reviews'])->flush();
    }
}
