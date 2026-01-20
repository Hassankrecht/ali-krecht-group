<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartApiController extends Controller
{
    public function get(Request $request)
    {
        $cart = session('cart', []);
        return response()->json(['cart' => $cart]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $cart = session('cart', []);
        $product = Product::find($request->product_id);
        $cart[$product->id] = [
            'product_id' => $product->id,
            'title' => $product->title ?? $product->title_localized,
            'title_localized' => $product->title_localized ?? $product->title,
            'price' => $product->price,
            'quantity' => $request->quantity,
        ];
        session(['cart' => $cart]);
        return response()->json(['cart' => $cart]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $cart = session('cart', []);
        unset($cart[$request->product_id]);
        session(['cart' => $cart]);
        return response()->json(['cart' => $cart]);
    }

    public function clear(Request $request)
    {
        session()->forget('cart');
        return response()->json(['cart' => []]);
    }
}
