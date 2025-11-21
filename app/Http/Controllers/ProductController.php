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
        // جلب جميع الفئات
        $categories = Category::all();

        // فلترة بالفئة إن وجدت
        $categoryId = $request->query('category');

        $products = Product::with('images', 'category')
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('products.index', compact('products', 'categories', 'categoryId'));
    }


    // عرض صفحة منتج واحد مع جميع الصور والتفاصيل
    // ProductController.php
    public function show($id)
    {
        $product = Product::with('images')->findOrFail($id);

        // تجهيز الصورة الرئيسية (main image)
        $mainImage = $product->image ? $product->image : null;

        return view('products.show', compact('product', 'mainImage'));
    }
}
