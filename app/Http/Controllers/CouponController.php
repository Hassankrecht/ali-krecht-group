<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Checkout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $code = $request->input('code');
        $now  = Carbon::now();
        $user = Auth::user();
        $cartItems = session('cart', []);
        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            return back()->with('error', 'Coupon not found.');
        }

        // حد الاستخدام لكل مستخدم يعتمد على سجل الطلبات
        if ($user && $coupon->user_usage_limit && $coupon->user_usage_limit > 0) {
            $alreadyUsed = Checkout::where('user_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->count();
            if ($alreadyUsed >= $coupon->user_usage_limit) {
                return back()->with('error', 'You have reached the usage limit for this coupon.');
            }
        }

        // صلاحية المستخدم
        if ($coupon->user_id && (!$user || $coupon->user_id !== $user->id)) {
            return back()->with('error', 'This coupon is not assigned to your account.');
        }

        if (!$coupon->status) {
            return back()->with('error', 'Coupon is inactive.');
        }

        if ($coupon->starts_at && $now->lt(Carbon::parse($coupon->starts_at))) {
            return back()->with('error', 'Coupon not started yet.');
        }

        if ($coupon->expiration_date && $now->gt(Carbon::parse($coupon->expiration_date))) {
            return back()->with('error', 'Coupon expired.');
        }

        if ($coupon->usage_limit > 0 && $coupon->used_count >= $coupon->usage_limit) {
            return back()->with('error', 'Coupon usage limit reached.');
        }

        if ($coupon->min_total && $subtotal < $coupon->min_total) {
            return back()->with('error', 'Order total is below minimum required.');
        }

        $discount = 0;
        if ($coupon->type === 'percent') {
            $discount = round($subtotal * ($coupon->value / 100), 2);
        } else {
            $discount = $coupon->value;
        }
        $discount = min($discount, $subtotal);

        session(['coupon' => [
            'code' => $coupon->code,
            'coupon_id' => $coupon->id,
            'discount' => $discount,
        ]]);

        return back()->with('success', 'Coupon applied.');
    }

    public function remove()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }
}
