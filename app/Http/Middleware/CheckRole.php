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
            'Owner' => ['dashboard', 'my-properties', 'contracts', 'dashboard/settings'],
            'Tenant' => ['dashboard','search-properties','appoinments'],
            'Owner' => ['dashboard', 'dashboard/settings', 'my-properties', 'contracts', 'contracts-details/{employee}', 'all-contracts', 'EvaluateRequest', 'appointments', 'maintenanceOwner'],
            'Tenant' => ['dashboard','search-properties','appointments','TrackRequest', 'maintenance/new'],
        ];

        $currentPath = $request->path();

        // Verificar si la ruta actual está permitida para el rol del usuario
        if (isset($allowedRoutes[$user->role]) && in_array($currentPath, $allowedRoutes[$user->role])) {
            return $next($request);
        }

        // Redirigir al usuario si no tiene permiso para acceder a la ruta
        return redirect('/dashboard');
    }
}