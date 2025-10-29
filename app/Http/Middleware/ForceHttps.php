<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        // Trust proxy headers for HTTPS
        Request::setTrustedProxies(
            [$request->getClientIp()],
            SymfonyRequest::HEADER_X_FORWARDED_PROTO | SymfonyRequest::HEADER_X_FORWARDED_HOST
        );

        // Force HTTPS only in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
