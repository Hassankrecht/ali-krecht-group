<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->nameFor($this->resource, 'en'),
            'name_ar' => $this->nameFor($this->resource, 'ar'),
            'description' => null,
            'description_ar' => null,
            'icon' => null,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'name' => $this->nameFor($this->parent, 'en'),
                    'name_ar' => $this->nameFor($this->parent, 'ar'),
                    'parent_id' => $this->parent->parent_id,
                ];
            }),
            'children' => $this->whenLoaded('children', function () {
                return $this->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $this->nameFor($child, 'en'),
                        'name_ar' => $this->nameFor($child, 'ar'),
                        'parent_id' => $child->parent_id,
                    ];
                })->values();
            }),
            'is_active' => true,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function nameFor($category, string $locale): string
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
}
