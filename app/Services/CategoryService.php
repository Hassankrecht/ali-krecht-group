<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all categories with relationships
     */
    public function getAllCategories(bool $withChildren = false): Collection
    {
        $query = Category::query();

        if ($withChildren) {
            $query->with('children');
        }

        return $query->get();
    }

    /**
     * Get parent categories only
     */
    public function getParentCategories(): Collection
    {
        return Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->get();
    }

    /**
     * Get category tree (hierarchical)
     */
    public function getCategoryTree(): Collection
    {
        return Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->with('children')->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById(int $id): ?Category
    {
        return Category::with(['children', 'products'])->find($id);
    }

    /**
     * Get category by slug
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)
            ->with(['children', 'products'])
            ->first();
    }

    /**
     * Create new category
     */
    public function create(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
            'order' => $data['order'] ?? 0,
            'slug' => $data['slug'] ?? str()->slug($data['name']),
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Update category
     */
    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'] ?? $category->name,
            'parent_id' => $data['parent_id'] ?? $category->parent_id,
            'order' => $data['order'] ?? $category->order,
            'slug' => $data['slug'] ?? str()->slug($data['name'] ?? $category->name),
            'description' => $data['description'] ?? $category->description,
        ]);

        return $category;
    }

    /**
     * Delete category
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * Get category with products (or all products if categoryId is null)
     */
    public function getWithProducts(?int $categoryId = null, int $perPage = 15)
    {
        $query = Product::with(['images', 'translations']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->simplePaginate($perPage);
    }

    /**
     * Get category breadcrumbs
     */
    public function getBreadcrumbs(Category $category): array
    {
        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);

            $current = $current->parent;
        }

        return $breadcrumbs;
    }

    /**
     * Get all descendants of a category
     */
    public function getDescendants(Category $category): Collection
    {
        $descendants = [];

        foreach ($category->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $this->getDescendants($child)->toArray());
        }

        return collect($descendants);
    }

    /**
     * Reorder categories
     */
    public function reorder(array $orders): bool
    {
        foreach ($orders as $order => $categoryId) {
            Category::where('id', $categoryId)->update(['order' => $order]);
        }

        return true;
    }

    /**
     * Get category statistics
     */
    public function getStats(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'product_count' => $category->products()->count(),
            'children_count' => $category->children()->count(),
            'total_stock' => $category->products()->sum('stock'),
            'average_price' => $category->products()->avg('price'),
        ];
    }

    /**
     * Get popular categories
     */
    public function getPopular(int $limit = 5): Collection
    {
        return Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Flatten category hierarchy
     */
    public function flatten(): Collection
    {
        $categories = collect();
        $allCategories = $this->getAllCategories();

        foreach ($allCategories as $category) {
            $categories->push($category);
            $this->flattenHelper($category, $categories);
        }

        return $categories;
    }

    /**
     * Helper for flattening
     */
    private function flattenHelper(Category $category, Collection &$collection): void
    {
        foreach ($category->children as $child) {
            $collection->push($child);
            $this->flattenHelper($child, $collection);
        }
    }
}
