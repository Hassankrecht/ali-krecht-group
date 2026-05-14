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
            'name' => $this->localizedName($this->resource),
            'description' => null,
            'icon' => null,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'name' => $this->localizedName($this->parent),
                    'parent_id' => $this->parent->parent_id,
                ];
            }),
            'children' => $this->whenLoaded('children', function () {
                return $this->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $this->localizedName($child),
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
    private function localizedName($category): string
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
}


