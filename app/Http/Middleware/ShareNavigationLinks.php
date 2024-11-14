<?php

namespace App\Http\Middleware;

use Inertia\Inertia;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareNavigationLinks
{
    public function handle(Request $request, Closure $next)
    {
        Inertia::share('navLinks', function () {
            $allowedRoutes = [
                'Owner' => ['dashboard', 'houses'],
                'Tenant' => ['dashboard'],
            ];

            $userRole = Auth::user() ? Auth::user()->role : null;

            // Si el usuario es un administrador o superadministrador, devolver todas las rutas
            if (in_array($userRole, ['admin', 'superadmin'])) {
                return array_values(array_unique(array_merge(...array_values($allowedRoutes))));
            }

            return $allowedRoutes[$userRole] ?? [];
        });

        return $next($request);
    }
}