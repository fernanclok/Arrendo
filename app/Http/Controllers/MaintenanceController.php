<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Contract;
use App\Models\Maintenance_request;
use App\Models\Property;

class MaintenanceController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'tenant_user_id' => 'required|exists:users,id',
                'type' => 'required|string|max:1000',
                'description' => 'required|string|max:1000',
                'priority' => 'required|in:Low,Medium,High',
                'evidence' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:10048',
            ]);
    
            // Procesar el archivo de evidencia, si existe
            $evidencePath = null;
            if ($request->hasFile('evidence')) {
                $file = $request->file('evidence');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $timestamp = now()->format('YmdHis');
                $newName = "{$originalName}_{$timestamp}.{$extension}";
    
                $destinationPath = public_path('evidences');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
    
                $file->move($destinationPath, $newName);
                $evidencePath = "evidences/{$newName}";
            }
    
            // Crear la solicitud de mantenimiento
            $maintenanceRequest = Maintenance_request::create([
                'tenant_user_id' => $validated['tenant_user_id'],
                'property_id' => $validated['property_id'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'report_date' => now(),
                'status' => 'Pending',
                'evidence' => $evidencePath,
            ]);
    
            // Respuesta de éxito
            return response()->json([
                'message' => 'Maintenance request submitted successfully.',
                'maintenanceRequest' => $maintenanceRequest,
            ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id', // Validar el parámetro
        ]);
    
        // Obtener las solicitudes del usuario autenticado con sus relaciones
        $maintenanceRequests = Maintenance_request::where('tenant_user_id', $request->user_id)
            ->with('property') // Relación property
            ->orderBy('priority', 'desc') // Ordenar según prioridad
            ->get();
    
        // Devolver respuesta JSON
        return response()->json($maintenanceRequests);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:Low,Medium,High',
        ]);
        $maintenanceRequest = Maintenance_request::findOrFail($id);
        $maintenanceRequest->update($validated);
        return response()->json([
            'message' => 'Maintenance request updated successfully.',
            'maintenanceRequest' => $maintenanceRequest,
        ]);
    }

    public function getPropertyByTenant(Request $request)
    {
        // Validar que se reciba el tenant_user_id
        $validated = $request->validate([
            'tenant_user_id' => 'required|exists:users,id',
        ]);
    
        // Buscar el contrato activo con su propiedad asociada
        $contract = Contract::with('Property')
            ->where('tenant_user_id', $validated['tenant_user_id'])
            ->where('status', 'Active')
            ->first();
    
        // Validar si el contrato o propiedad existe
        if (!$contract || !$contract->Property) {
            return response()->json([
                'error' => !$contract ? 'No active contract found.' : 'No property associated with this contract.',
            ], 404);
        }
    
        // Devolver los datos de la propiedad asociada
        return response()->json([
            'property' => [
                'id' => $contract->Property->id,
                'street' => $contract->Property->street,
                'number' => $contract->Property->number,
                'city' => $contract->Property->city,
                'state' => $contract->Property->state,
            ],
        ], 200);
    }
   
//Function for my owner in Maintenance
    public function getRequestsByProperty(Request $request)
    {
        // Validar que el parámetro property_id esté presente
        $request->validate([
            'property_id' => 'required|integer|exists:properties,id', // Validar que exista en la tabla properties
        ]);

        // Obtener el ID de la propiedad
        $propertyId = $request->query('property_id');

        // Consultar las solicitudes de mantenimiento asociadas a la propiedad
        //$maintenanceRequests = Maintenance_request::where('property_id', $propertyId)->get();
        $maintenanceRequests = Maintenance_request::with(['property', 'tenantUser']) // Cargar relaciones
        ->where('property_id', $propertyId)
        ->get();
        // Devolver la respuesta en formato JSON
        return response()->json($maintenanceRequests);
    }
    public function updateRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed',
            'owner_note' => 'nullable|string|max:255',
            'maintenance_cost' => 'nullable|numeric|min:0',
        ]);

        $maintenanceRequest = Maintenance_request::findOrFail($id);
        $maintenanceRequest->status = $validated['status'];
        $maintenanceRequest->owner_note = $validated['owner_note'] ?? null;
        $maintenanceRequest->maintenance_cost = $validated['maintenance_cost'] ?? null;
        $maintenanceRequest->date_review = now();
        $maintenanceRequest->save();
        // Devolver una respuesta exitosa
        return response()->json([
            'message' => 'Maintenance request updated successfully.',
            'maintenanceRequest' => $maintenanceRequest,
        ]);
    }

}


