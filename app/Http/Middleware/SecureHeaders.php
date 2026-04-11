<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip security headers for AJAX/API requests (performance optimization)
        if ($request->ajax() || $request->wantsJson()) {
            return $response;
        }

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Enforce HTTPS
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Content Security Policy - cached for performance
        static $cspPolicy;
        if (!$cspPolicy) {
            $cspPolicy = "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com http://localhost:5173 http://localhost:5174; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://fonts.bunny.net; " .
                "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://fonts.bunny.net; " .
                "img-src 'self' data: https: blob:; " .
                "connect-src 'self' http://localhost:5173 http://localhost:5174 ws://localhost:5173 ws://localhost:5174 https://fonts.googleapis.com https://fonts.gstatic.com;";
        }
        $response->headers->set('Content-Security-Policy', $cspPolicy);

        // Permissions Policy (formerly Feature-Policy)
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );

        return $response;
    }
}
