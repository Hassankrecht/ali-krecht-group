<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslation;

class ProjectCategoryTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $locales = config('app.supported_locales', ['en']);
        $fallback = config('app.locale', 'en');

        ProjectCategory::with('translations')->chunk(50, function ($cats) use ($locales, $fallback) {
            foreach ($cats as $cat) {
                foreach ($locales as $locale) {
                    $data = ['name' => $cat->name];
                    $existing = $cat->translations->firstWhere('locale', $locale);

                    if ($locale === $fallback) {
                        $existing
                            ? $existing->update($data)
                            : ProjectCategoryTranslation::create($data + ['project_category_id' => $cat->id, 'locale' => $locale]);
                        continue;
                    }

                    if (!$existing) {
                        ProjectCategoryTranslation::create($data + ['project_category_id' => $cat->id, 'locale' => $locale]);
                    }
                }
            }
        });
    }
}
