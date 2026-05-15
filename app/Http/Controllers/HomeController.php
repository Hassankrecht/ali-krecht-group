<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Product;
use App\Models\Review;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\ProjectCategory;
use App\Models\Checkout;
use App\Models\HomeSetting;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * الصفحة الرئيسية
     */
    public function index()
    {
        $homeSettings = HomeSetting::first();

        // آخر 6 مشاريع مع الصور المرتبطة بها (Eager Loading)
        $projects = Project::with(['images', 'translations'])
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        // دالة مساعدة لتطبيع المسار وإرجاع رابط صالح
        $resolvePath = function (?string $path, $default = null) {
            if (!$path) {
                return $default ?: asset('assets/img/default.jpg');
            }

            // تصحيح المسارات القديمة storage/public/assets/... إلى assets/...
            if (str_contains($path, 'storage/public/assets/')) {
                $path = str_replace('storage/public/', '', $path);
            }

            // إذا كان المسار يبدأ بـ assets/ أو public/ استخدمه مباشرة
            if (str_starts_with($path, 'assets/') || str_starts_with($path, 'public/')) {
                $candidate = public_path(str_starts_with($path, 'public/') ? substr($path, strlen('public/')) : $path);
                if (file_exists($candidate)) {
                    return asset($path);
                }
            }

            // تحقق من تخزين القرص العام
            if (Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }

            // مسارات قديمة داخل assets/img أو img
            foreach (['assets/img/', 'img/'] as $prefix) {
                if (file_exists(public_path($prefix . $path))) {
                    return asset($prefix . $path);
                }
            }

            return $default ?: asset('assets/img/default.jpg');
        };

        // احسب روابط الصور والمجموعات مسبقًا لتخفيف منطق العرض داخل Blade
        $projects = $projects->map(function ($project) use ($resolvePath) {
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

        // فئات المشاريع (هرمية)
        $projectParents = ProjectCategory::with(['translations', 'children.translations'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
        $projectChildren = ProjectCategory::with('translations')
            ->whereNotNull('parent_id')
            ->orderBy('order')
            ->get();

        if ($projectParents->isEmpty() && $projectChildren->isEmpty()) {
            $projectParents = ProjectCategory::with('translations')->get()->map(function ($cat) {
                $cat->setRelation('children', collect());
                return $cat;
            });
            $projectChildren = $projectParents;
        }

        // مشاريع مميزة لكل فئة فرعية (حتى 4 عناصر)
        $projectsByCategory = [];
        foreach ($projectChildren as $cat) {
            $items = Project::with(['images', 'translations', 'categories'])
                ->whereHas('categories', fn($q) => $q->where('project_category_id', $cat->id))
                ->orderBy('id', 'desc')
                ->take(4)
                ->get()
                ->map(function ($project) use ($resolvePath) {
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

            $projectsByCategory[$cat->id] = $items;
        }

        // فئات المنتجات (هرمية)
        $parentCategories = Category::with(['translations', 'children.translations'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
        $childCategories = Category::with(['translations'])
            ->whereNotNull('parent_id')
            ->orderBy('order')
            ->get();

        // fallback: إذا لا توجد فئات فرعية، اعتبر جميع الفئات كفرعية تحت Parent افتراضي
        if ($parentCategories->isEmpty() && $childCategories->isEmpty()) {
            $parentCategories = Category::with('translations')->get()->map(function ($cat) {
                $cat->setRelation('children', collect());
                return $cat;
            });
            $childCategories = $parentCategories;
        }

        // الفئات التي يمكن عرض منتجاتها في الصفحة الرئيسية:
        // فئات فرعية + أي فئة رئيسية لا تحتوي على فئات فرعية.
        $productDisplayCategories = $childCategories
            ->concat($parentCategories->filter(fn($category) => $category->children->isEmpty()))
            ->unique('id')
            ->values();

        // جلب آخر 4 منتجات لكل فئة قابلة للعرض
        $productsByCategory = [];
        foreach ($productDisplayCategories as $category) {
            $productsByCategory[$category->id] = Product::with(['images', 'translations', 'category.translations'])
                ->where('category_id', $category->id)
                ->orderBy('id', 'desc')
                ->take(4)
                ->get();
        }





        // آخر 4 مراجعات العملاء (يمكن تعديل العدد أو استخدام paginate)
        $reviewsQuery = Review::where('is_approved', true);

        $reviews = (clone $reviewsQuery)->orderBy('id', 'desc')
            ->take(4)
            ->get();
        $reviewsAvg = round((clone $reviewsQuery)->avg('rating') ?? 0, 1);
        $reviewsCount = (clone $reviewsQuery)->count();

        // بيانات الخدمات (منفصلة عن الـ Blade)
        // خدمات ثابتة من ملف الإعدادات مع مفاتيح الترجمة والروابط
        $services = collect(config('services', []))
            ->take(4) // نعرض 4 خدمات في الصفحة الرئيسية
            ->map(function ($service) {
                $key = $service['translation_key'] ?? null;
                return [
                    'icon'    => $service['icon'] ?? null,
                    'slug'    => $service['slug'] ?? null,
                    'title'   => $key ? __($key . '.title') : '',
                    'excerpt' => $key ? __($key . '.excerpt') : '',
                    'highlight' => $key ? __($key . '.highlight') : '',
                ];
            })->toArray();

        // بيانات الفريق (منفصلة عن الـ Blade)
        $team = [
            [
                'name'  => 'ALi Krecht',
                'job'   => 'General Manager',
                'photo' => asset('assets/img/pexels-mastercowley-1300402.jpg'),
            ],
            [
                'name'  => 'Hassan Krecht',
                'job'   => 'Marketing and Sales Manager',
                'photo' => asset('assets/img/pexels-mastercowley-1300402.jpg'),
            ],
            [
                'name'  => 'Bilal Ahmed',
                'job'   => 'Corporate Manager',
                'photo' => asset('assets/img/pexels-mastercowley-1300402.jpg'),
            ],
            [
                'name'  => 'Abdo Krecht',
                'job'   => 'Manager Operations',
                'photo' => asset('assets/img/pexels-brett-sayles-1073097.jpg'),
            ],
        ];

        // تمرير الفئات: استخدام الرئيسية للتوافق مع العروض القديمة
        $categories = $parentCategories;

        return view('home', [
            'projects' => $projects,
            'projectParents' => $projectParents,
            'projectChildren' => $projectChildren,
            'projectsByCategory' => $projectsByCategory,
            'categories' => $parentCategories, // للتوافق القديم
            'parentCategories' => $parentCategories,
            'childCategories' => $childCategories,
            'productDisplayCategories' => $productDisplayCategories,
            'productsByCategory' => $productsByCategory,
            'reviews' => $reviews,
            'services' => $services,
            'team' => $team,
            'reviewsAvg' => $reviewsAvg,
            'reviewsCount' => $reviewsCount,
            'homeSettings' => $homeSettings,
        ]);
    }

    /**
     * Store visitor review
     */
    public function storeReview(Request $request)
    {
        $siteKey = env('RECAPTCHA_SITE_KEY');
        $secret  = env('RECAPTCHA_SECRET') ?: env('RECAPTCHA_SECRET_KEY');
        $recaptchaRule = ($siteKey && $secret)
            ? ['required', new Recaptcha]
            : ['nullable'];

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'rating'     => 'required|integer|min:1|max:5',
            'review'     => 'required|string|max:1000',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'g-recaptcha-response' => $recaptchaRule,
        ]);

        // Handle optional photo upload
        if ($request->hasFile('photo')) {
            $dir = public_path('assets/reviews');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $name = $request->file('photo')->hashName();
            $request->file('photo')->move($dir, $name);
            // نخزن المسار بشكل موحد assets/...
            $data['photo'] = "assets/reviews/{$name}";
        }

        // Keep new submissions pending until an admin approves
        $data['is_approved'] = false;

        Review::create($data);

        return back()->with('review_success', __('messages.home.testimonials_submitted') ?? 'Thank you! Your review was submitted.');
    }

    /**
     * صفحة من نحن
     */
    public function about()
    {
        return view('about');
    }

    /**
     * صفحة الخدمات
     */
    public function services()
    {
        $services = collect(config('services', []))->map(function ($service) {
            $key = $service['translation_key'] ?? null;
            return [
                'slug'    => $service['slug'] ?? '',
                'icon'    => $service['icon'] ?? null,
                'image'   => $service['image'] ?? null,
                'gallery' => $service['gallery'] ?? [],
                'title'   => $key ? __($key . '.title') : '',
                'excerpt' => $key ? __($key . '.excerpt') : '',
            ];
        });
        return view('services', compact('services'));
    }

    /**
     * صفحة الاتصال
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * صفحة خطوات العمل Process
     */
    public function process()
    {
        return view('process');
    }

    /**
     * صفحة الأسعار/التقديرات
     */
    public function pricing()
    {
        $services = collect(config('services', []))->map(function ($service) {
            $key = $service['translation_key'] ?? null;
            $features = $service['features'] ?? [];
            $process  = $service['process_steps'] ?? [];

            // ترجمة العنوان والوصف إن توفر مفتاح
            $title   = $key ? __($key . '.title')   : ($service['slug'] ?? '');
            $excerpt = $key ? __($key . '.excerpt') : ($service['excerpt'] ?? '');
            $highlight = $key ? __($key . '.highlight') : ($service['highlight'] ?? $excerpt);

            if (empty($features) && $key) {
                $features = __($key . '.features') ?? [];
            }
            if (!is_array($features)) {
                $features = array_filter([$features]);
            }

            if (empty($process) && $key) {
                $process = __($key . '.process') ?? [];
            }
            if (!is_array($process)) {
                $process = array_filter([$process]);
            }

            return [
                'slug'      => $service['slug'] ?? '',
                'icon'      => $service['icon'] ?? null,
                'title'     => $title,
                'excerpt'   => $excerpt,
                'highlight' => $highlight,
                'features'  => $features,
                'process'   => $process,
            ];
        });

        return view('pricing', compact('services'));
    }

    /**
     * صفحة الآراء (التقييمات)
     */
    public function testimonials()
    {
        $reviewsQuery = Review::where('is_approved', true);

        $reviews = (clone $reviewsQuery)->orderBy('id', 'desc')->paginate(12);
        $avg = round((clone $reviewsQuery)->avg('rating') ?? 0, 1);
        $count = (clone $reviewsQuery)->count();

        return view('testimonials', [
            'reviews' => $reviews,
            'reviewsAvg' => $avg,
            'reviewsCount' => $count,
        ]);
    }

    /**
     * لوحة المستخدم
     */
    public function dashboard()
    {
        $user = auth()->user();
        $coupons = $user
            ? Coupon::where('user_id', $user->id)->orderByDesc('created_at')->get()
            : collect();

        return view('user.dashboard', compact('coupons'));
    }

    /**
     * صفحة طلبات المستخدم
     */
    public function orders()
    {
        $user = auth()->user();
        $orders = Checkout::with(['coupon','items'])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('user.orders', compact('orders'));
    }

    /**
     * صفحة ملف المستخدم
     */
    public function profile()
    {
        $user = auth()->user();
        return view('user.profile', compact('user'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:25',
            'country' => 'nullable|string|max:100',
            'town' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone_number = $data['phone_number'] ?? $user->phone_number;
        $user->country = $data['country'] ?? $user->country;
        $user->town = $data['town'] ?? $user->town;
        $user->zipcode = $data['zipcode'] ?? $user->zipcode;
        $user->address = $data['address'] ?? $user->address;
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
