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
            'Owner' => ['dashboard', 'dashboard/settings', 'my-properties', 'contracts', 'contracts-details/{employee}', 'all-contracts', 'EvaluateRequest', 'appointments', 'appointment-request', 'maintenanceOwner', 'my-invoices'],
            'Tenant' => ['dashboard','search-properties','appointments','TrackRequest', 'maintenance'],
        ];

        $currentPath = $request->path();

        // Verificar si la ruta actual está permitida para el rol del usuario
        if (isset($allowedRoutes[$user->role]) && in_array($currentPath, $allowedRoutes[$user->role])) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if (isset($allowedRoutes[$role])) {
                foreach ($allowedRoutes[$role] as $allowedRoute) {
                    if (strpos($allowedRoute, '{contracts}') !== false) {
                        $pattern = str_replace('{contracts}', '[0-9]+', $allowedRoute);

                        if (preg_match("#^$pattern$#", $currentPath)) {
                            return $next($request);
                        }
                    } elseif ($currentPath === $allowedRoute) {
                        return $next($request);
                    }
                }
            }
        }

        // Redirigir al usuario si no tiene permiso para acceder a la ruta
        return redirect('/dashboard');
    }
}