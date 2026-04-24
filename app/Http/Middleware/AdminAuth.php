<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Allow only sessions flagged as the single admin.
     * Anyone else is redirected to the admin login screen.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('is_admin')) {
            return redirect()->route('admin.login.show');
        }

        return $next($request);
    }
}
