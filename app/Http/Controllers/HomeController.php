<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Product;
use App\Models\Review;
use App\Models\Coupon;
use App\Models\Category;
class HomeController extends Controller
{
    /**
     * الصفحة الرئيسية
     */
    public function index()
    {
        // آخر 6 مشاريع مع الصور المرتبطة بها (Eager Loading)
        $projects = Project::with('images')
                    ->orderBy('id', 'desc')
                    ->take(6)
                    ->get();

        // آخر 6 منتجات
         $categories = Category::all();

        // جلب آخر 4 منتجات من كل فئة
        $productsByCategory = [];
        foreach ($categories as $category) {
            $productsByCategory[$category->id] = Product::where('category_id', $category->id)
                ->orderBy('id', 'desc')
                ->take(4)
                ->get();
        }


       
    

        // آخر 4 مراجعات العملاء (يمكن تعديل العدد أو استخدام paginate)
        $reviews = Review::orderBy('id', 'desc')
                    ->take(4)
                    ->get();

        // الكوبونات النشطة، مع حد 10 للسلامة
       
        return view('home', compact('projects', 'categories', 'productsByCategory', 'reviews'));
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
        return view('services');
    }

    /**
     * صفحة الاتصال
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * لوحة المستخدم
     */
    public function dashboard()
    {
        return view('user.dashboard');
    }
}
