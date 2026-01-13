<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectCategory;
use Illuminate\Support\Str;

class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Carpentry' => ['Doors', 'Bedrooms', 'Kitchens', 'Wardrobes', 'Custom Furniture'],
            'Construction' => ['Renovation', 'Villas', 'Commercial Fit-out', 'Maintenance'],
            'Decoration' => ['Interiors', 'Lighting', 'Metal & Glass'],
        ];

        $order = 0;
        foreach ($tree as $parentName => $children) {
            $parent = ProjectCategory::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                ['name' => $parentName, 'order' => $order++]
            );
            $childOrder = 0;
            foreach ($children as $childName) {
                ProjectCategory::firstOrCreate(
                    ['slug' => Str::slug($parentName . ' ' . $childName)],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'order' => $childOrder++,
                    ]
                );
            }
        }
    }
}
