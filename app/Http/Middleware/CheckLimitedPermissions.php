<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLimitedPermissions
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (Auth::check()) {
            foreach ($permissions as $permission) {
                if (Auth::user()->can($permission)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Acceso no autorizado');
    }
}