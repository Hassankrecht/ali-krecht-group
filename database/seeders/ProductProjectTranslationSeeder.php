<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Project;
use App\Models\ProjectTranslation;

class ProductProjectTranslationSeeder extends Seeder
{
    /**
     * Seed translations for products and projects.
     * This copies existing title/description into translations
     * for locales that are missing (en, ar, pt).
     */
    public function run(): void
    {
        $locales = config('app.supported_locales', ['en']);
        $fallback = config('app.locale', 'en');

        // Products
        Product::with('translations')->chunk(50, function ($products) use ($locales, $fallback) {
            foreach ($products as $product) {
                foreach ($locales as $locale) {
                    $data = [
                        'title'       => $product->title,
                        'description' => $product->description,
                    ];

                    // Always sync fallback locale to base fields
                    if ($locale === $fallback) {
                        $product->translations()->updateOrCreate(
                            ['locale' => $locale],
                            $data
                        );
                        continue;
                    }

                    // Other locales: create if missing, leave existing translations intact
                    if (!$product->translations->firstWhere('locale', $locale)) {
                        $product->translations()->create([
                            'locale'      => $locale,
                            'title'       => $data['title'],
                            'description' => $data['description'],
                        ]);
                    }
                }
            }
        });

        // Projects
        Project::with('translations')->chunk(50, function ($projects) use ($locales, $fallback) {
            foreach ($projects as $project) {
                foreach ($locales as $locale) {
                    $data = [
                        'title'       => $project->title,
                        'description' => $project->description,
                    ];

                    // Sync fallback locale to base fields
                    if ($locale === $fallback) {
                        $project->translations()->updateOrCreate(
                            ['locale' => $locale],
                            $data
                        );
                        continue;
                    }

                    // Other locales: create if missing only
                    if (!$project->translations->firstWhere('locale', $locale)) {
                        $project->translations()->create([
                            'locale'      => $locale,
                            'title'       => $data['title'],
                            'description' => $data['description'],
                        ]);
                    }
                }
            }
        });
    }
}
