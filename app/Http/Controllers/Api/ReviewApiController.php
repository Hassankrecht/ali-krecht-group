<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewApiController extends Controller
{
    public function index($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $reviews = $product->reviews()->where('is_approved', true)->latest()->paginate(10);
        return response()->json($reviews);
    }

    public function store(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $request->validate([
            'review' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        $review = new Review([
            'user_id' => Auth::id(),
            'review' => $request->review,
            'rating' => $request->rating,
            'is_approved' => false,
        ]);
        $product->reviews()->save($review);
        return response()->json(['message' => 'Review submitted and pending approval.'], 201);
    }
}
