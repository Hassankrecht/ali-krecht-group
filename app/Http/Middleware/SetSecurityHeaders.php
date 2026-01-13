<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetSecurityHeaders
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
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // Prevent Clickjacking
        $response->header('X-Frame-Options', 'DENY');

        // Enable XSS Protection
        $response->header('X-XSS-Protection', '1; mode=block');

        // Content Security Policy
        // Allows: Google Fonts, Google Analytics, Google reCAPTCHA, Tawk.to, CDN resources
        $response->header('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                "cdn.jsdelivr.net " .
                "code.jquery.com " .
                "www.google.com " .
                "www.gstatic.com " .
                "www.recaptcha.net " .
                "cdn.jsdelivr.net/npm/chart.js " .
                "cdn.jsdelivr.net/npm/swiper " .
                "cdnjs.cloudflare.com " .
                "js.stripe.com " .
                "embed.tawk.to " .
                "cdn.tawk.to; " .
            "style-src 'self' 'unsafe-inline' " .
                "cdn.jsdelivr.net " .
                "fonts.googleapis.com " .
                "cdnjs.cloudflare.com " .
                "embed.tawk.to; " .
            "img-src 'self' data: https: " .
                "www.google.com " .
                "www.gravatar.com " .
                "cdn.jsdelivr.net; " .
            "font-src 'self' " .
                "fonts.gstatic.com " .
                "cdnjs.cloudflare.com; " .
            "connect-src 'self' " .
                "www.google.com " .
                "www.gstatic.com " .
                "api.tawk.to " .
                "cdn.tawk.to; " .
            "frame-src 'self' " .
                "www.recaptcha.net " .
                "embed.tawk.to; " .
            "frame-ancestors 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self';"
        );

        // Referrer Policy
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy (formerly Feature-Policy)
        $response->header('Permissions-Policy', 
            'geolocation=(), ' .
            'microphone=(), ' .
            'camera=(), ' .
            'payment=(), ' .
            'usb=(), ' .
            'magnetometer=(), ' .
            'gyroscope=(), ' .
            'accelerometer=()'
        );

        // HSTS (HTTP Strict-Transport-Security)
        // Only enable in production with HTTPS
        if (config('app.env') === 'production') {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Additional Security Headers
        $response->header('X-Permitted-Cross-Domain-Policies', 'none');
        $response->header('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->header('Cross-Origin-Opener-Policy', 'same-origin');
        $response->header('Cross-Origin-Resource-Policy', 'same-origin');

        return $response;
    }
}
