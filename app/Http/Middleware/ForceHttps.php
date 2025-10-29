<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        // Trust Render's proxy headers
        $request->setTrustedProxies(
            [$request->getClientIp()],
            Request::HEADER_X_FORWARDED_ALL
        );

        // Force HTTPS only in production
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
