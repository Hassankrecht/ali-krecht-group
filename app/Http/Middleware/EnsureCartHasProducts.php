<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class EnsureCartHasProducts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Assuming cart is stored in session as 'cart' and is an array of products
        $cart = session('cart', []);
        if (empty($cart) || count($cart) === 0) {
            if (Auth::check()) {
                $hasDbCart = Cart::where('user_id', Auth::id())->exists();
                if ($hasDbCart) {
                    return $next($request);
                }
            }
            // Optionally, you can add a flash message here
            return redirect()->route('cart.index');
        }
        return $next($request);
    }
}
