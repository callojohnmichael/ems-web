<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $roleName = (string) config('site_settings.super_admin_role', 'admin');

        abort_unless($user && $user->hasRole($roleName), 403);

        return $next($request);
    }
}
