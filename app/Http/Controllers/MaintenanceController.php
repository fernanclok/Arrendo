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
    public function create()
    {
        $contract = Contract::with('Property')
            ->where('tenant_user_id', auth()->id())
            ->where('status', 'Active')
            ->first();
    
        if (!$contract || !$contract->Property) {
            return redirect()->back()->withErrors([
                'error' => !$contract ? 'No active contract found.' : 'No property associated with this contract.',
            ]);
        }
    
        return Inertia::render('Maintenance/CreateMaintenance', [
            'Property' => [
                'id' => $contract->Property->id,
                'street' => $contract->Property->street,
            ],
        ]);
    }
    
    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'tenant_user_id' => 'required|exists:users,id',
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
    
    /**
     * 
     */
    public function edit($id)
    {
        // Lógica pendiente
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

    /**
     * 
     */
    public function destroy($id)
    {
       
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
    // Validar que el ID sea válido y que el status sea permitido
    $request->validate([
        'status' => 'required|in:Pending,In Progress,Completed',
    ]);

    // Buscar la solicitud por ID
    $maintenanceRequest = Maintenance_request::findOrFail($id);

    // Actualizar el estado
    $maintenanceRequest->status = $request->input('status');
    $maintenanceRequest->save();

    // Devolver una respuesta exitosa
    return response()->json([
        'message' => 'Maintenance request updated successfully.',
        'maintenanceRequest' => $maintenanceRequest,
    ]);
    }

}


