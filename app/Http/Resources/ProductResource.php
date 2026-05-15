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
        $englishTranslation = $this->translationFor('en');
        $arabicTranslation = $this->translationFor('ar');

        $title = $englishTranslation?->title ?? $this->title;
        $titleAr = $arabicTranslation?->title ?? $title;
        $description = $englishTranslation?->description
            ?? ($this->hasArabic($this->description) ? null : $this->description);
        $descriptionAr = $arabicTranslation?->description ?? $description;
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
            'name_ar' => $titleAr,
            'title' => $title,
            'title_ar' => $titleAr,
            'description' => $description,
            'description_ar' => $descriptionAr,
            'price' => $this->price,
            'image' => $mainImage,
            'gallery' => $gallery,
            'images' => $gallery,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->categoryNameFor($this->category, 'en'),
                    'name_ar' => $this->categoryNameFor($this->category, 'ar'),
                    'parent_id' => $this->category->parent_id,
                ];
            }),
            'stock' => $this->stock ?? 0,
            'is_active' => true,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function translationFor(string $locale)
    {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        return $this->translations->firstWhere('locale', $locale);
    }

    private function categoryNameFor($category, string $locale): string
    {
        if ($category && $category->relationLoaded('translations')) {
            $translation = $category->translations->firstWhere('locale', $locale);

            if ($translation?->name) {
                return $translation->name;
            }
        }

        $fallback = $category->name_localized ?? $category->name ?? '';

        if ($locale === 'en' && $this->hasArabic($fallback)) {
            return '';
        }

        return $fallback;
    }

    private function hasArabic(?string $value): bool
    {
        return is_string($value) && preg_match('/[\x{0600}-\x{06FF}]/u', $value) === 1;
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
