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
public function show($id)
{
   
}
    /**
     * 
     */
    public function edit($id)
    {
        // Lógica pendiente
    }

    /**
     * Actualizar el estado o los datos de una solicitud.
     */
    public function update(Request $request, $id)
    {
        // Lógica pendiente
    }

    /**
     * 
     */
    public function destroy($id)
    {
       
    }

}