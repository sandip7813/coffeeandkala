<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Ensure the authenticated user has the given permission (or is super admin).
     *
     * Usage: ->middleware('permission:manage-users')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        abort_unless(
            $user !== null && ($user->isSuperAdmin() || $user->hasPermission($permission)),
            403,
        );

        return $next($request);
    }
}
