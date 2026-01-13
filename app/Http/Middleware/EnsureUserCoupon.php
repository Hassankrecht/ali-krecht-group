<?php

namespace App\Http\Middleware;

use App\Models\Coupon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EnsureUserCoupon
{
    /**
     * Create a welcome coupon for logged-in users if none is active.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // تم تعطيل الإنشاء التلقائي للكوبونات
        return $response;
    }
}
