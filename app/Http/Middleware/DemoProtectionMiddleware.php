<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DemoProtectionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('demo')) {

            $protectedRoutes = [
                'department.store',
                'department.update',
                'department.destroy',
                'employee.store',
                'employee.update',
                'employee.destroy',
                'user.store',
                'user.update',
                'user.destroy',
                'password.update',
            ];

            if ($request->route() && in_array($request->route()->getName(), $protectedRoutes, true)) {
                throw new HttpException(403, 'This action is disabled in demo mode.');
            }      
        }

        return $next($request);
    }
}
