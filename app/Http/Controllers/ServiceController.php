<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Product;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function show($slug)
    {
        $services = collect(config('services', []));
        $service = $services->firstWhere('slug', $slug);

        if (!$service) {
            abort(404);
        }

        $key = $service['translation_key'] ?? null;
        $slug = $service['slug'] ?? null;
        $title = $key ? __($key . '.title') : ($slug ?? 'Service');
        $excerpt = $key ? __($key . '.excerpt') : '';
        $highlight = $key ? __($key . '.highlight') : '';
        $features = $key ? __($key . '.features') : [];
        $process = $key ? __($key . '.process') : [];
        $cta = $key ? __($key . '.cta') : '';

        $features = is_array($features) ? $features : array_filter([$features]);
        $process = is_array($process) ? $process : array_filter([$process]);

        $serviceData = [
            'slug'     => $slug,
            'icon'     => $service['icon'] ?? '',
            'image'    => $service['image'] ?? 'assets/img/default.jpg',
            'gallery'  => $service['gallery'] ?? [],
            'title'    => $title,
            'excerpt'  => $excerpt,
            'highlight'=> $highlight ?: $excerpt,
            'features' => $features,
            'process_steps'  => $process,
            'cta'      => $cta ?: __('messages.services_page.cta'),
            'category_slug' => $service['category_slug'] ?? $slug,
        ];

        // مشاريع مرتبطة بالتصنيف (إن وجد category_slug)
        $projects = collect();
        if (!empty($service['category_slug'])) {
            $projects = Project::with(['images', 'translations', 'categories'])
                ->whereHas('categories', fn($q) => $q->where('slug', $service['category_slug']))
                ->orderBy('id', 'desc')
                ->take(6)
                ->get()
                ->map(function ($project) {
                    $imagePath = $project->main_image ?? ($project->images->first()->image_path ?? null);
                    if ($imagePath && file_exists(public_path('storage/' . $imagePath))) {
                        $mainImage = asset('storage/' . $imagePath);
                    } elseif ($imagePath && file_exists(public_path('assets/img/' . $imagePath))) {
                        $mainImage = asset('assets/img/' . $imagePath);
                    } else {
                        $mainImage = asset('assets/img/default.jpg');
                    }
                    $project->setAttribute('main_image_url', $mainImage);
                    return $project;
                });
        }

        // منتجات مرتبطة بالتصنيف (إن وجد category_slug) — نستخدم الاسم/الترجمات بدلاً من slug لأنه غير موجود بجدول التصنيفات الحالية
        $products = collect();
        if (!empty($service['category_slug'])) {
            $products = Product::with(['images', 'translations', 'category.translations'])
                ->whereHas('category', function ($q) use ($service) {
                    $q->where('name', $service['category_slug'])
                      ->orWhereHas('translations', fn($t) => $t->where('name', $service['category_slug']));
                })
                ->orderBy('id', 'desc')
                ->take(6)
                ->get();
        }

        return view('services.show', [
            'service' => $serviceData,
            'projects' => $projects,
            'products' => $products,
        ]);
    }
}
