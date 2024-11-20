<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use GuzzleHttp\Psr7\Response;

class ContractController extends Controller
{

    // Insert contract information
    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validatedData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'tenant_user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'rental_amount' => 'required|numeric',
                'status' => 'required',
                'contract_path.*' => 'required|file|mimes:pdf|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Crear un nuevo contrato
            $contract = new Contract();
            $contract->property_id = $validatedData['property_id'];
            $contract->tenant_user_id = $validatedData['tenant_user_id'];
            $contract->start_date = $validatedData['start_date'];
            $contract->end_date = $validatedData['end_date'];
            $contract->rental_amount = $validatedData['rental_amount'];
            $contract->status = $validatedData['status'];

            // Guardar los documentos del contrato
            if ($request->hasFile('contract_path')) {
                $contract_path = [];
                foreach ($request->file('contract_path') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = now()->format('YmdHis');
                    $newName = "{$originalName}_{$timestamp}.{$extension}";
                    $destinationPath = public_path('contracts_files');
                    $file->move($destinationPath, $newName);
                    $contract_path[] = "contracts/{$newName}";
                }
                $contract->contract_path = json_encode($contract_path);
            }

            $contract->owner_user_id = $request->user_id;
            $contract->save();

            // Devolver una respuesta JSON de éxito

            return response()->json([
                'status' => 'success',
                'message' => 'Contract created successfully'
            ]);
        } catch (\Exception $e) {
            // Devolver una respuesta JSON de error
            return response()->json(['success' => false, 'message' => $e,], 500);
        }
    }
    // Get all contracts
    public function index()
    {
        //obtener todos los contratos y sus relaciones
        $contracts = Contract::with('property', 'tenantUser')->get();
        // Devolver una respuesta JSON de éxito
        return response()->json(['contracts' => $contracts]);
    }
}
