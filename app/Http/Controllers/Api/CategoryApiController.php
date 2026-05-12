<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with(['translations', 'parent.translations', 'children.translations'])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function parents(Request $request)
    {
        $categories = Category::with(['translations', 'children.translations'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show($id)
    {
        $category = Category::with(['translations', 'parent.translations', 'children.translations'])
            ->findOrFail($id);

        return new CategoryResource($category);
    }
}
