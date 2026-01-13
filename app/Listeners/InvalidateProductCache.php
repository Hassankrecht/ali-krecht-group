<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use Illuminate\Support\Facades\Cache;

class InvalidateProductCache
{
    /**
     * Handle ProductCreated event
     */
    public function handleCreated(ProductCreated $event)
    {
        $this->invalidateCache();
    }

    /**
     * Handle ProductUpdated event
     */
    public function handleUpdated(ProductUpdated $event)
    {
        $this->invalidateCache();

        // Clear specific product cache if exists
        Cache::forget("product.{$event->product->id}");
    }

    /**
     * Invalidate all product-related caches
     */
    private function invalidateCache()
    {
        // Get all cache versions to clear
        $cacheVersions = config('cache.versions', ['1']);
        $locales = config('app.supported_locales', ['en', 'ar']);

        foreach ($cacheVersions as $version) {
            foreach ($locales as $locale) {
                // Clear product-related caches
                Cache::forget("home.products.{$version}.{$locale}");
                Cache::forget("home.productsByCategory.{$version}.{$locale}");
                Cache::forget("home.productCategories.{$version}.{$locale}");
                Cache::forget("home.reviews.{$version}.{$locale}");
            }
        }

        // Clear all product lists
        Cache::tags(['products'])->flush();
    }
}
