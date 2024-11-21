<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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
                'contract_files.*' => 'required|file|mimes:pdf,png,jpg,jpeg',
                'owner_user_id' => 'required|exists:users,id',
                'status' => 'required',
            ]);

            // Crear un nuevo contrato
            $contract = new Contract();
            $contract->property_id = $validatedData['property_id'];
            $contract->tenant_user_id = $validatedData['tenant_user_id'];
            $contract->start_date = $validatedData['start_date'];
            $contract->end_date = $validatedData['end_date'];
            $contract->rental_amount = $validatedData['rental_amount'];


            if ($request->hasFile('contract_files')) {
                $files = [];
                foreach ($request->file('contract_files') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = now()->format('YmdHis');
                    $newName = "{$originalName}_{$timestamp}.{$extension}";
                    $destinationPath = public_path('contracts_files');

                    // Crear la carpeta si no existe
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    $file->move($destinationPath, $newName);
                    $files[] = "contracts/{$newName}";
                }
                $contract->contract_path = json_encode($files);
            }


            $contract->owner_user_id = $validatedData['owner_user_id'];
            $contract->status = $validatedData['status'];
            $contract->save();

            // Devolver una respuesta JSON de éxito

            return redirect()->route('/contracts')->with('success', 'Contract created successfully');
        } catch (\Exception $e) {
            // Devolver una respuesta JSON de error
            return response()->json(['success' => false, 'message' => $e,], 500);
        }
    }
    // Get all contracts
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);
        //obtener todos los contratos del usuario autenticado con sus relaciones
        $contracts = Contract::where('owner_user_id', $request->user_id)
            ->with('property', 'tenantUser')
            ->get();
        // Devolver una respuesta JSON de éxito
        return response()->json($contracts);
    }
    public function getTenantUsers()
    {
        // Obtener todos los usuarios con el rol de 'Tenant'
        $tenantUsers = User::where('role', 'Tenant')->get();
        // Devolver una respuesta JSON de éxito
        return response()->json($tenantUsers);
    }
}
