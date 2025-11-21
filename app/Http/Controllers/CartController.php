<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * عرض السلة.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', ['cartItems' => $cart]);
    }

    /**
     * إضافة منتج إلى السلة.
     */
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        $name = $product->title ?? 'Unnamed Product';
        $imagePath = $product->image ?? null;

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'product_id' => $product->id,
                'title'      => $name,
                'price'      => $product->price ?? 0,
                'quantity'   => 1,
                'image'      => $imagePath,
            ];
        }

        $this->updateCartSession($cart);

        return redirect()->back()->with('success', "{$name} added to cart!");
    }

    /**
     * زيادة الكمية.
     */
    public function increase($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        }

        $this->updateCartSession($cart);
        if (request()->expectsJson()) {
            return response()->json($this->buildCartResponse($cart, $id));
        }

        return redirect()->route('cart.index')->with('success', 'Quantity increased.');
    }

    /**
     * تقليل الكمية أو حذف العنصر إذا وصلت إلى 0.
     */
    public function decrease($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            } else {
                unset($cart[$id]);
            }
        }

        $this->updateCartSession($cart);
        if (request()->expectsJson()) {
            return response()->json($this->buildCartResponse($cart, $id));
        }

        return redirect()->route('cart.index')->with('success', 'Quantity updated.');
    }

    /**
     * إزالة منتج من السلة.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $this->updateCartSession($cart);

        if (request()->expectsJson()) {
            return response()->json($this->buildCartResponse($cart, $id, true));
        }

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    /**
     * عرض صفحة الدفع (Checkout).
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('checkout.index', [
            'cartItems' => $cart,
            'total'     => $total
        ]);
    }

    /**
     * دالة خاصة لتحديث معلومات السلة في الـ session.
     */
    private function updateCartSession($cart)
    {
        session()->put('cart', $cart);

        // ✅ اجمع الكميات وليس فقط عدد الأصناف
        $totalQuantity = collect($cart)->sum(fn($i) => $i['quantity']);
        $totalPrice = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        session()->put('cart_count', $totalQuantity);
        session()->put('cart_total', $totalPrice);

        session()->save(); // 🔥 ضروري لتحديث النافبار مباشرة
    }

    /**
     * تجهيز رد JSON مهيكل لتحديث الواجهة بدون إعادة تحميل.
     */
    private function buildCartResponse(array $cart, $itemId, bool $removed = false): array
    {
        $itemQuantity = isset($cart[$itemId]) ? $cart[$itemId]['quantity'] : 0;
        $itemTotal = isset($cart[$itemId]) ? $cart[$itemId]['price'] * $cart[$itemId]['quantity'] : 0;
        $grandTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $count = collect($cart)->sum(fn($i) => $i['quantity']);

        return [
            'removed' => $removed || !isset($cart[$itemId]),
            'item_id' => $itemId,
            'item_quantity' => $itemQuantity,
            'item_total' => number_format($itemTotal, 2),
            'grand_total' => number_format($grandTotal, 2),
            'grand_total_raw' => $grandTotal,
            'count' => $count,
        ];
    }
}
