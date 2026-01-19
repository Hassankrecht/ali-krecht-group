<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Review;
use App\Models\ProjectCategory;
use Illuminate\Support\Facades\Auth;
use App\Rules\Recaptcha;

class ProjectController extends Controller
{
    /**
     * عرض صفحة مشروع واحد
     */
    public function show($id)
    {
        $project = Project::with('images')->findOrFail($id);
        return view('projects.show', compact('project'));
    }

    /**
     * عرض جميع المشاريع (أو أحدث 6 مشاريع)
     */
    public function index()
    {
        $categorySlug = request('category');
        $categories = ProjectCategory::with(['children.translations','translations'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        // تحديد الفئة المطلوبة (أب أو ابن)
        $category = null;
        if ($categorySlug) {
            $category = ProjectCategory::with('children')->where('slug', $categorySlug)->first();
        }

        $projects = Project::with(['images', 'translations', 'categories'])
            ->when($category, function ($q) use ($category) {
                if (is_null($category->parent_id)) {
                    // فئة أب: اجلب كل المشاريع المرتبطة بأبنائها (أو بها إن لم يكن لها أبناء)
                    $childIds = $category->children->pluck('id');
                    $ids = $childIds->isNotEmpty() ? $childIds : collect([$category->id]);
                    $q->whereHas('categories', fn($c) => $c->whereIn('project_category_id', $ids));
                } else {
                    // فئة ابن
                    $q->whereHas('categories', fn($c) => $c->where('project_category_id', $category->id));
                }
            })
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->through(function ($project) {
                $resolvePath = function (?string $path) {
                    if (!$path) return asset('assets/img/default.jpg');
                    if (str_contains($path, 'storage/public/assets/')) {
                        $path = str_replace('storage/public/', '', $path);
                    }
                    if (str_starts_with($path, 'assets/') || str_starts_with($path, 'public/')) {
                        $candidate = public_path(str_starts_with($path, 'public/') ? substr($path, strlen('public/')) : $path);
                        if (file_exists($candidate)) {
                            return asset($path);
                        }
                    }
                    if (file_exists(public_path('storage/' . $path))) {
                        return asset('storage/' . $path);
                    }
                    foreach (['assets/img/', 'img/'] as $prefix) {
                        if (file_exists(public_path($prefix . $path))) {
                            return asset($prefix . $path);
                        }
                    }
                    return asset('assets/img/default.jpg');
                };

                $imagePath = $project->main_image ?? ($project->images->first()->image_path ?? null);
                $mainImage = $resolvePath($imagePath);

                $gallery = $project->images
                    ->pluck('image_path')
                    ->filter()
                    ->map(fn($img) => $resolvePath($img))
                    ->values();

                $project->setAttribute('main_image_url', $mainImage);
                $project->setAttribute('gallery_urls', $gallery);

                return $project;
            });

        $reviews = Review::where('is_approved', true)
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        return view('projects.index', compact('projects', 'reviews', 'categories', 'categorySlug'));
    }

    /**
     * عرض صفحة إنشاء مراجعة
     */
    public function createReview()
    {
        return view('reviews.create');
    }

    /**
     * حفظ المراجعة الجديدة
     */
    public function storeReview(Request $request)
    {
        $siteKey = env('RECAPTCHA_SITE_KEY');
        $secret  = env('RECAPTCHA_SECRET') ?: env('RECAPTCHA_SECRET_KEY');
        $recaptchaRule = ($siteKey && $secret)
            ? ['required', new Recaptcha]
            : ['nullable'];

        $validated = $request->validate([
            'name' => 'required',
            'review' => 'required',
            'g-recaptcha-response' => $recaptchaRule,
        ]);

        // إذا كان المستخدم مسجل الدخول، نربط المراجعة بحسابه
        if(Auth::check()){
            $validated['user_id'] = Auth::id();
        }

        // Keep submissions pending until an admin approves
        $validated['is_approved'] = false;

        Review::create($validated);

        return redirect()->back()->with('success','Review submitted successfully.');
    }
}
