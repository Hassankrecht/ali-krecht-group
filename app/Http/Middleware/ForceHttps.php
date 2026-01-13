<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    /**
     * Redirect HTTP to HTTPS in production.
     */
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('production') && !$request->secure()) {
            $secureUrl = 'https://' . $request->getHttpHost() . $request->getRequestUri();
            return redirect()->secure($secureUrl, 301);
        }

        return $next($request);
    }
}
