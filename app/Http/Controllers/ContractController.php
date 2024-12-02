<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Property;
use App\Models\Contract_renewal;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Rental_application;

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

            // Llamar a la función para generar las facturas para este contrato
            $this->generateInvoices($contract->id);


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

        // Obtener todos los contratos del usuario autenticado con sus relaciones
        $contracts = Contract::where('owner_user_id', $request->user_id)
            ->with('property', 'tenantUser')
            // Ordenar los contratos por los que estén por vencer
            ->orderBy('end_date', 'asc')
            ->get();

        // Si no existe el id en owner_user_id buscar con tenant_user_id
        if ($contracts->isEmpty()) {
            $contracts = Contract::where('tenant_user_id', $request->user_id)
                ->with('property', 'tenantUser')
                // Ordenar los contratos por los que esten y sigan vigentes
                ->orderBy('end_date', 'desc')
                ->get();
        }

        // Devolver una respuesta JSON de éxito
        return response()->json($contracts);
    }
    public function getTenantUsers(Request $request)
    {
        // validar los datos del formulario
        $request->validate([
            'property_id' => 'required|string|exists:properties,id',
        ]);

        // Obtener todos los usuarios que han solicitado la propiedad
        $tenantUsers1 = Rental_application::where('property_id', $request->property_id)
            ->where('status', 'Approved')
            ->with('tenantUser')
            ->get();

        // filtrar la información de los usuarios
        $tenantUsers = $tenantUsers1->map(function ($tenantUser) {
            return $tenantUser->tenantUser;
        });

        // Devolver una respuesta JSON de éxito
        return response()->json($tenantUsers);
    }

    public function getProperties()
    {
        // Obtener todas las propiedades que no están disponibles
        $properties = Property::join('zones', 'zones.id', '=', 'properties.zone_id')
            ->select('properties.*', 'zones.name as zone_name')
            ->where('availability', 'Not Available')
            ->get()
            ->map(function ($property) {
                $photos = $property->property_photos_path ? json_decode($property->property_photos_path, true) : [];
                $property->property_photos_path = is_array($photos)
                    ? array_map(fn($photo) => asset($photo), $photos)
                    : [];
                return $property;
            });

        // Filtrar las propiedades que no tienen contratos activos
        $properties = $properties->filter(function ($property) {
            return !Contract::where('property_id', $property->id)
                ->where('status', 'Active')
                ->exists();
        });

        return response()->json($properties);
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


    // Función para generar las facturas
    public function generateInvoices($contractId)
    {
        try {
            // Obtener el contrato
            $contract = Contract::findOrFail($contractId);

            $startDate = Carbon::parse($contract->start_date);
            $endDate = Carbon::parse($contract->end_date);
            $totalAmount = $contract->rental_amount;

            $invoices = [];
            while ($startDate <= $endDate) {
                // Crear una nueva factura para cada mes
                $invoice = new Invoice([
                    'contract_id' => $contractId,
                    'issue_date' => $startDate->format('Y-m-d'),
                    'total_amount' => $totalAmount,
                    'payment_status' => 'Pending', // Estado inicial de la factura
                ]);

                // Guardar la factura
                $invoice->save();
                $invoices[] = $invoice;

                // Incrementar la fecha al siguiente mes
                $startDate->addMonth();
            }

            // Devolver las facturas generadas
            return response()->json($invoices, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generating invoices', 'details' => $e->getMessage()], 500);
        }
    }
}
