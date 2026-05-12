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
            'name' => $this->englishName($this->resource),
            'description' => null,
            'icon' => null,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'name' => $this->englishName($this->parent),
                    'parent_id' => $this->parent->parent_id,
                ];
            }),
            'children' => $this->whenLoaded('children', function () {
                return $this->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $this->englishName($child),
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
    private function englishName($category): string
    {
        if ($category && $category->relationLoaded('translations')) {
            $translation = $category->translations->firstWhere('locale', 'en');
            if ($translation?->name) {
                return $translation->name;
            }
        }

        return $category->name_localized ?? $category->name ?? '';
    }
}


