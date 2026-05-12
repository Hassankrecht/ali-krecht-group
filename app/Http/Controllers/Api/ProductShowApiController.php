<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductShowApiController extends Controller
{
    /**
     * Display a single product by ID.
     * @param int $id
     * @return ProductResource|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with(['images', 'category.translations', 'translations'])->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return new ProductResource($product);
    }
}
