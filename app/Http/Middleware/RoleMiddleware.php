<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // NOT LOGGED IN
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->roleid;

        // If no roles specified, deny by default (safe)
        if (empty($roles)) {
            abort(403, 'Unauthorized Access');
        }

        // Check if user role is allowed
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized Access');
        }

        return $next($request);
    }
}