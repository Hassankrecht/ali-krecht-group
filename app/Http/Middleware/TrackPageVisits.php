<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;

class TrackPageVisits
{
    /**
     * Log simple page visits into the page_visits table.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // Skip admin, assets, and non-GET requests.
            if (
                !$request->isMethod('get') ||
                $request->is('admin/*') ||
                $request->is('storage/*') ||
                $request->is('vendor/*') ||
                $request->is('ignition/*')
            ) {
                return $response;
            }

            PageVisit::create([
                'path'       => $request->path(),
                'ip'         => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 500),
            ]);
        } catch (\Throwable $e) {
            // Do not break the request if logging fails.
        }

        return $response;
    }
}
