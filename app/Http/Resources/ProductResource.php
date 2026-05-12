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
        $english = $this->relationLoaded('translations')
            ? $this->translations->firstWhere('locale', 'en')
            : null;
        $title = $english?->title ?? $this->title;
        $description = $english?->description ?? $this->description;
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
                    'name' => $this->englishCategoryName($this->category),
                    'parent_id' => $this->category->parent_id,
                ];
            }),
            'stock' => $this->stock ?? 0,
            'is_active' => true,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function englishCategoryName($category): string
    {
        if ($category && $category->relationLoaded('translations')) {
            $translation = $category->translations->firstWhere('locale', 'en');
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



