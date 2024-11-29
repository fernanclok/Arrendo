<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Contract_renewal;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

                    // Reemplazar espacios por guiones bajos o quitarlos
                    $sanitizedOriginalName = preg_replace('/\s+/', '_', $originalName);
                    $newName = "{$sanitizedOriginalName}_{$timestamp}.{$extension}";
                    $destinationPath = public_path('contracts_files');

                    // Crear la carpeta si no existe
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    $file->move($destinationPath, $newName);
                    $files[] = "contracts_files/{$newName}";
                }
                $contract->contract_path = json_encode($files);
            }


            $contract->owner_user_id = $validatedData['owner_user_id'];
            $contract->status = $validatedData['status'];
            $contract->save();

            // Devolver una respuesta JSON de éxito
            return response()->json(['message' => 'Contract created successfully'], 201);
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
            // Ordenar los contratos por los que esten por vencer
            ->orderBy('end_date', 'asc')
            ->get();
        // Devolver una respuesta JSON de éxito
        return response()->json($contracts);
    }
    public function getTenantUsers()
    {
        try {
            $tenantUsers = User::where('role', 'Tenant')->get(); // Devolver una respuesta JSON de éxito
            // Devolver una respuesta JSON de éxito
            return response()->json($tenantUsers);
        } catch (\Exception $e) {
            // Registrar el error y devolver una respuesta JSON de error
            Log::error('Error fetching tenant users: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching tenant users'], 500);
        }
    }

    public function getContract($id)
    {
        // Obtener un contrato por su ID con sus relaciones
        $contract = Contract::where('id', $id)->with('property', 'tenantUser')->first();
        // Devolver una respuesta JSON de éxito
        return response()->json($contract);
    }
    // Method to check and update contract statuses
    public function checkAndUpdateContractStatuses()
    {
        try {
            // Obtener todos los contratos cuya fecha de finalización ha pasado y cuyo estado no es 'pending renewal'
            $contracts = Contract::where('end_date', '<', Carbon::now())
                ->where('status', '!=', 'Pending Renewal')
                ->get();

            foreach ($contracts as $contract) {
                $contract->status = 'Pending Renewal';
                $contract->save();
            }

            // Devolver una respuesta JSON de éxito
            return response()->json(['message' => 'Contract statuses updated successfully'], 200);
        } catch (\Exception $e) {
            // Registrar el error y devolver una respuesta JSON de error
            Log::error('Error updating contract statuses: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating contract statuses'], 500);
        }
    }
    // Method  to renew a contract
    public function renewContract(Request $request, $id)
    {
        // insertat en la tabla de contracts_renewals y actualizar el status del contrato a Active
        try {
            // Validar los datos del formulario
            $validatedData = $request->validate([
                'contract_id' => 'required|exists:contracts,id',
                'renewal_start_date' => 'required|date',
                'renewal_end_date' => 'required|date|after:renewal_start_date',
                'renewal_rental_amount' => 'required|numeric',
                'renewal_status' => 'required',
            ]);
            // Crear un nuevo contrato de renovación
            $contractRenewal = new Contract_renewal();
            $contractRenewal->contract_id = $validatedData['contract_id'];
            $contractRenewal->renewal_start_date = $validatedData['renewal_start_date'];
            $contractRenewal->renewal_end_date = $validatedData['renewal_end_date'];
            $contractRenewal->renewal_rental_amount = $validatedData['renewal_rental_amount'];
            $contractRenewal->renewal_status = $validatedData['renewal_status'];
            $contractRenewal->save();

            // Actualizar el contrato original
            $originalContract = Contract::find($id);
            if ($originalContract) {
                $originalContract->status = 'Active';
                $originalContract->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Original contract not found'], 404);
            }

            // Devolver una respuesta JSON de éxito
            return response()->json(['message' => 'Contract renewed successfully'], 201);
        } catch (\Exception $e) {
            // Registrar el error y devolver una respuesta JSON de error
            Log::error('Error renewing contract: ' . $e->getMessage());
            return response()->json(['error' => 'Error renewing contract', 'message' => $e->getMessage()], 500);
        }
    }

    // Meethod to terminate a contract
    public function terminateContract($id)
    {
        // Actualizar el estado del contrato a Terminated
        try {
            $contract = Contract::find($id);
            $contract->status = 'Terminated';
            $contract->save();

            // Devolver una respuesta JSON de éxito
            return response()->json(['message' => 'Contract terminated successfully'], 200);
        } catch (\Exception $e) {
            // Devolver una respuesta JSON de error
            return response()->json(['success' => false, 'message' => $e,], 500);
        }
    }
}
