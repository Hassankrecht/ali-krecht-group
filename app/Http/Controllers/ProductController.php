<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category; // تأكد من وجود هذا الموديل
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // عرض جميع المنتجات مع pagination والفئات
    public function index(Request $request)
    {
        $parentCategories = Category::with(['translations', 'children.translations'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
        $childCategories = Category::with('translations')
            ->whereNotNull('parent_id')
            ->orderBy('order')
            ->get();

        if ($parentCategories->isEmpty() && $childCategories->isEmpty()) {
            $parentCategories = Category::with('translations')->get()->map(function ($cat) {
                $cat->setRelation('children', collect());
                return $cat;
            });
            $childCategories = $parentCategories;
        }

        // فلترة بالفئة إن وجدت (باستخدام id للفئة الفرعية)
        $categoryId = $request->query('category');

        $products = Product::with(['images', 'category.translations', 'translations'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('products.index', [
            'products' => $products,
            'parentCategories' => $parentCategories,
            'childCategories' => $childCategories,
            'categoryId' => $categoryId,
        ]);
    }


    // عرض صفحة منتج واحد مع جميع الصور والتفاصيل
    // ProductController.php
    public function show($id)
    {
        $product = Product::with(['images', 'translations'])->findOrFail($id);

        // تجهيز الصورة الرئيسية (main image)
        $mainImage = $product->image ? $product->image : null;

        return view('products.show', compact('product', 'mainImage'));
    }
}
