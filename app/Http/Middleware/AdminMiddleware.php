<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // allow multiple roles in future (scalable)
        $allowedRoles = [1]; // 1 = Admin

        if (!in_array(auth()->user()->roleid, $allowedRoles)) {
            abort(403, 'Unauthorized Access');
        }

        return $next($request);
    }
}