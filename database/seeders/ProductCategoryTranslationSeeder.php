<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\ProductCategoryTranslation;

class ProductCategoryTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $locales = config('app.supported_locales', ['en']);
        $fallback = config('app.locale', 'en');

        Category::with('translations')->chunk(50, function ($cats) use ($locales, $fallback) {
            foreach ($cats as $cat) {
                foreach ($locales as $locale) {
                    $data = ['name' => $cat->name];
                    $existing = $cat->translations->firstWhere('locale', $locale);

                    if ($locale === $fallback) {
                        $existing
                            ? $existing->update($data)
                            : ProductCategoryTranslation::create($data + ['category_id' => $cat->id, 'locale' => $locale]);
                        continue;
                    }

                    if (!$existing) {
                        ProductCategoryTranslation::create($data + ['category_id' => $cat->id, 'locale' => $locale]);
                    }
                }
            }
        });
    }
}
