<?php

namespace App\Http\Middleware\MiddlewareWeb;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class loginCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $loginStatus = session()->get('loginStatus');
        if ($loginStatus) {
            return redirect()->route('dashboard');
        } else {
            return $next($request);
        }
    }
}
