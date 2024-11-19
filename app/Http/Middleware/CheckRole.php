<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/');
        }

        // Si el usuario es un administrador, permitir el acceso inmediatamente
        if (in_array($user->role, ['admin', 'superadmin'])) {
            return $next($request);
        }

        $allowedRoutes = [
            'Owner' => ['dashboard', 'manage/contracts','dashboard/settings'],
            'Tenant' => ['dashboard', 'appoinments'],
        ];

        $currentPath = $request->path();

        foreach ($roles as $role) {
            if (isset($allowedRoutes[$role])) {
                foreach ($allowedRoutes[$role] as $allowedRoute) {
                    if (strpos($allowedRoute, '{employee}') !== false) {
                        $pattern = str_replace('{employee}', '[0-9]+', $allowedRoute);

                        if (preg_match("#^$pattern$#", $currentPath)) {
                            return $next($request);
                        }
                    } elseif ($currentPath === $allowedRoute) {
                        return $next($request);
                    }
                }
            }
        }

        return redirect('/');
    }
}
