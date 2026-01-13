<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    /**
     * Get parent/child categories with translations.
     */
    public function getCategoryTree(): array
    {
        $parents = Category::with(['translations', 'children.translations'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        $children = Category::with('translations')
            ->whereNotNull('parent_id')
            ->orderBy('order')
            ->get();

        if ($parents->isEmpty() && $children->isEmpty()) {
            $parents = Category::with('translations')->get()->map(function ($cat) {
                $cat->setRelation('children', collect());
                return $cat;
            });
            $children = $parents;
        }

        return [$parents, $children];
    }

    /**
     * Paginate products optionally filtered by category.
     */
    public function list(?int $categoryId, int $perPage = 20): LengthAwarePaginator
    {
        $page    = request()->integer('page', 1);
        $locale  = app()->getLocale();
        $version = Cache::get('products.cache.version', 1);
        $key     = "products.list.v{$version}.c{$categoryId}.p{$page}.pp{$perPage}.{$locale}";

        return Cache::remember($key, 3600, function () use ($categoryId, $perPage) {
            return Product::with(['images', 'category.translations', 'translations'])
                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                ->orderBy('id', 'desc')
                ->paginate($perPage);
        });
    }

    /**
     * Find single product with relations.
     */
    public function findWithRelations(int $id): Product
    {
        $locale  = app()->getLocale();
        $version = Cache::get('products.cache.version', 1);
        $key     = "products.show.v{$version}.{$id}.{$locale}";

        return Cache::remember($key, 3600, function () use ($id) {
            return Product::with(['images', 'translations'])->findOrFail($id);
        });
    }

    /**
     * أحدث المنتجات (لأقسام مميزة/هوم) مع كاش قابل للإبطال.
     */
    public function featured(int $limit = 12): Collection
    {
        $locale  = app()->getLocale();
        $version = Cache::get('products.cache.version', 1);
        $key     = "products.featured.v{$version}.l{$limit}.{$locale}";

        return Cache::remember($key, 3600, function () use ($limit) {
            return Product::with(['images', 'translations'])
                ->orderBy('id', 'desc')
                ->take($limit)
                ->get();
        });
    }

    /**
     * إبطال جميع كاش المنتجات (زيادة النسخة).
     */
    public function bumpCacheVersion(): void
    {
        if (!Cache::has('products.cache.version')) {
            Cache::forever('products.cache.version', 1);
            return;
        }

        Cache::increment('products.cache.version');
    }
}
