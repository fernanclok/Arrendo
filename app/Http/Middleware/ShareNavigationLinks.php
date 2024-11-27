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
                'Owner' => [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'monitor-dashboard'],
                    ['route' => 'AllContracts', 'label' => 'contracts', 'icon' => 'file'],
                    ['route' => 'appointments', 'label' => 'Appointments', 'icon' => 'calendar'],
                    ['route' => 'myProperties', 'label' => 'My Properties', 'icon' => 'home'],
                    ['route' => 'maintenanceOwner','label' => 'Maintenance','icon'=> 'hammer-wrench'],
                    ['route' => 'EvaluateRequest', 'label' => 'Request', 'icon' => 'account-question']
                ],
                'Tenant' => [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'monitor-dashboard'],
                    ['route' => 'searchProperties', 'label' => 'Properties', 'icon' => 'home'],
                    ['route' => 'appointments', 'label' => 'Appointments', 'icon' => 'calendar'],
                    ['route' => 'maintenance','label' => 'Maintenance','icon'=> 'hammer-wrench'],
                    ['route' => 'TrackRequest', 'label' => 'Applications', 'icon' => 'account-question'],
                ],
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
