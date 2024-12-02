<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\Maintenance_request;
use Illuminate\Http\Request;

class AndroidStudioController extends Controller
{
    public function getUserRole(Request $request)
    {
        // Validar el request
        $request->validate([
            'firebase_uid' => 'required|string',
        ]);

        // Buscar al usuario
        $user = User::where('firebase_uid', $request->firebase_uid)->first();

        // Si no se encuentra el usuario, devolver error
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Devolver el rol y los datos del usuario
        return response()->json([
            'role' => $user->role,
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
        ]);
    }
    public function getMaintenanceRequests(Request $request)
    {
        // 1. Validar el UID recibido
        $request->validate([
            'firebase_uid' => 'required|string',
        ]);

        // 2. Buscar el usuario por su UID
        $user = User::where('firebase_uid', $request->firebase_uid)->first();

        if (!$user) {
            // Si no se encuentra el usuario, devolver un error
            return response()->json(['error' => 'User not found'], 404);
        }

        // 3. Consultar las solicitudes de mantenimiento asociadas
        $maintenanceRequests = Maintenance_request::where('tenant_user_id', $user->id)
            ->with('property') // Relación con propiedades
            ->orderBy('report_date', 'desc') // Ordenar por prioridad
            ->get();

        // 4. Devolver las solicitudes en formato JSON
        return response()->json($maintenanceRequests);
    }

    //Owner
    public function getOwnerProperties(Request $request)
    {
        // Validar que venga el UID en el request
        $request->validate([
            'firebase_uid' => 'required|string',
        ]);

        // Buscar el usuario por el UID
        $user = User::where('firebase_uid', $request->firebase_uid)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Buscar propiedades del Owner
        $properties = Property::where('owner_user_id', $user->id)->get();

        return response()->json($properties);
    }

    public function getPendingRequestsByProperty(Request $request)
    {
        // Validar que el parámetro property_id esté presente
        $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
        ]);

        // Obtener el ID de la propiedad
        $propertyId = $request->query('property_id');

        // Consultar las solicitudes de mantenimiento asociadas a la propiedad con estado "Pending"
        $pendingRequests = Maintenance_request::with(['property', 'tenantUser'])
            ->where('property_id', $propertyId)
            ->where('status', 'Pending') // Filtrar por estado "Pending"
            ->get();

        // Devolver la respuesta en formato JSON
        return response()->json($pendingRequests);
    }
}
