<?php

namespace App\Http\Middleware\MiddlewareWeb;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $requiredRole): Response
    {
        $loginStatus = session('loginStatus');
        $userRole = session('role');


        if (!$loginStatus || $userRole !== $requiredRole) {
            return redirect()->route('home')->withErrors(['unauthorized' => 'Akses tidak diizinkan']);
        }

        return $next($request);
    }
}
