<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedCustom
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // Redirect user or admin to their dashboard if already logged in
            if ($guard === 'admin') {
                return redirect()->route('dashboard'); // Change to your admin dashboard route if different
            }
            return redirect('/');
        }
        return $next($request);
    }
}
