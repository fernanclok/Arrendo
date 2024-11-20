<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Contract;
use App\Models\Maintenance_request;
class MaintenanceController extends Controller
{

    public function create()
{
    // Buscar el contrato activo del usuario logeado
    $contract = Contract::with('Property')
        ->where('tenant_user_id', auth()->id())
        ->where('status', 'Active')
        ->first();

    // Validar que el contrato exista
    if (!$contract) {
        abort(403, 'No active contract found.'); }
       // Validar que la propiedad asociada exista
       if (!$contract->Property) {
        abort(404, 'No property associated with this contract.');}
                    
    // Pasar el nombre de la propiedad asociada a la vista
    return Inertia::render('Maintenance/CreateMaintenance', [
        'Property' => [
            'id' => $contract->Property->id,
            'name' => $contract->Property->name,
        ],
    ]); // Nombre de la propiedad
}
    /**
     * 
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'property_id' => 'required|exists:properties,id',
        'description' => 'required|string|max:1000', 
        'priority' => 'required|in:Low,Medium,High', 
        'evidence' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:10048', 
    ]);

    // Almacenar la evidencia si se adjunta
    $evidencePath = $request->file('evidence') ? $request->file('evidence')->store('evidences') : null;

    $maintenanceRequest = Maintenance_request::create([
        'tenant_user_id' => auth()->id(),
        'property_id' => $validated['property_id'],
        'description' => $validated['description'],
        'priority' => $validated['priority'],
        'report_date' => now(),
        'status' => 'Pending',
        'evidence' => $evidencePath, // Ruta relativa
    ]);

    return redirect()->route('maintenance.show', ['id' => $maintenanceRequest->id])
    ->with('status', 'Maintenance request submitted successfully.');
        
}
public function show($id)
{
    // Busca la solicitud de mantenimiento por ID
    $maintenanceRequest = Maintenance_request::findOrFail($id);

    // Devuelve una vista o una respuesta con los detalles de la solicitud
    return response()->json(['message' => 'Show method is working', 'id' => $id]);
}
    /**
     * 
     */
    public function edit($id): Response
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