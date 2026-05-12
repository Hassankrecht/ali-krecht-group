<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Display a listing of products.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $products = Product::with(['images', 'category.translations', 'translations'])
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 20));

        return ProductResource::collection($products);
    }

    public function popular(Request $request)
    {
        $products = Product::with(['images', 'category.translations', 'translations'])
            ->orderByDesc('id')
            ->limit($request->integer('limit', 20))
            ->get();

        return ProductResource::collection($products);
    }

    public function featured(Request $request)
    {
        $products = Product::with(['images', 'category.translations', 'translations'])
            ->orderByDesc('id')
            ->limit($request->integer('limit', 20))
            ->get();

        return ProductResource::collection($products);
    }
}
