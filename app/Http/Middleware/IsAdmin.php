<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure the user is authenticated and has role 'admin'
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/students')->with('error', 'You are not authorized.');
        }

        abort(403, 'Unauthorized');
    }
}
