<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $response = $next($request);

         Log::info('CacheHeaders middleware running for: ' . $request->path());

        // Cache static assets for 1 year (31536000 seconds)
      if ($request->is('build/*') || $request->is('images/*')) {
                Log::info('Setting cache headers for: ' . $request->path());

            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
        } else {
            // HTML pages: allow bfcache, but validate on reload
            $response->header('Cache-Control', 'no-cache, private');
            $response->header('Pragma', 'no-cache');
            // Remove Expires header; it's optional
            $response->headers->remove('Expires');
        }

        return $response;
    }
}
