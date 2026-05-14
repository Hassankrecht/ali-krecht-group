<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Disable resource wrapping for single resource responses.
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');
        $translation = $this->relationLoaded('translations')
            ? $this->translations->firstWhere('locale', $locale)
                ?? $this->translations->firstWhere('locale', $fallback)
            : null;

        $title = $translation?->title ?? $this->title;
        $description = $translation?->description ?? $this->description;
        $gallery = $this->whenLoaded('images', function () {
            return $this->images
                ->sortBy('order')
                ->map(fn ($img) => $this->imageUrl($img->image))
                ->filter()
                ->values()
                ->all();
        }, []);
        $mainImage = $this->imageUrl($this->image) ?? ($gallery[0] ?? null);

        return [
            'id' => $this->id,
            'name' => $title,
            'title' => $this->title,
            'description' => $description,
            'price' => $this->price,
            'image' => $mainImage,
            'gallery' => $gallery,
            'images' => $gallery,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->localizedCategoryName($this->category),
                    'parent_id' => $this->category->parent_id,
                ];
            }),
            'stock' => $this->stock ?? 0,
            'is_active' => true,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function localizedCategoryName($category): string
    {
        if ($category && $category->relationLoaded('translations')) {
            $locale = app()->getLocale();
            $fallback = config('app.fallback_locale', 'en');
            $translation = $category->translations->firstWhere('locale', $locale)
                ?? $category->translations->firstWhere('locale', $fallback);

            if ($translation?->name) {
                return $translation->name;
            }
        }

        return $category->name_localized ?? $category->name ?? '';
    }
    private function imageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (str_starts_with($path, 'assets/') || str_starts_with($path, 'storage/')) {
            return url('api/media/' . $path);
        }

        return url('api/media/storage/' . $path);
    }
}



