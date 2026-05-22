<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Not logged in
        if (!session()->has('userid')) {
            return redirect()->route('login');
        }

        $userRole = session('roleid');

        // Role-based access check
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}