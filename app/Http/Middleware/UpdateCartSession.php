<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class UpdateCartSession
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // التحقق إن كان المستخدم قد سجل الدخول للتو
        if (Auth::check()) {
            $userId = Auth::id();

            // 1️⃣ إذا كانت هناك سلة في الـSession (ضيف)
            $sessionCart = session('cart', []);

            if (!empty($sessionCart)) {
                foreach ($sessionCart as $item) {
                    // تحقق إن كان المنتج موجود مسبقاً في السلة
                    $existing = Cart::where('user_id', $userId)
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if ($existing) {
                        // نزيد الكمية
                        $existing->quantity += $item['quantity'] ?? 1;
                        $existing->save();
                    } else {
                        // نضيف منتج جديد في السلة
                        Cart::create([
                            'user_id' => $userId,
                            'product_id' => $item['product_id'],
                            'name' => $item['title'] ?? $item['name'] ?? 'Unnamed Product',
                            'price' => $item['price'],
                            'quantity' => $item['quantity'] ?? 1,
                            'image' => $item['image'] ?? null,
                        ]);
                    }
                }

                // بعد الدمج، نحذف سلة الـSession
                session()->forget('cart');
            }

            // 2️⃣ نقرأ السلة الحالية من قاعدة البيانات
            $cartItems = Cart::where('user_id', $userId)->get();
        } else {
            // المستخدم ضيف: نستخدم سلة الـSession فقط
            $cartItems = collect(session('cart', []));
        }

        // 3️⃣ نحسب العدد والإجمالي
        $cartCount = $cartItems->count();
        $cartTotal = $cartItems->sum(function ($item) {
            return $item['price'] * ($item['quantity'] ?? 1);
        });

        // 4️⃣ نحفظها في الجلسة لتظهر في النافبار
        session([
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
        ]);

        return $response;
    }
}
