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
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $loginStatus = session('loginStatus');
        $userRole = session('role');

        // Mendukung satu atau banyak role, contoh: check.role:admin_super,admin
        if (!$loginStatus || !in_array($userRole, $roles, true)) {
            return redirect()->route('home')->withErrors(['unauthorized' => 'Akses tidak diizinkan']);
        }

        return $next($request);
    }
}
