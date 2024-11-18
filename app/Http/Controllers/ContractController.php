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
            ]);

            // Crear un nuevo contrato
            $contrato = Contract::create($validatedData);

            // Devolver una respuesta JSON de éxito
            return response()->json(['success' => 'Contract created successfully.']);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y mostrar el mensaje de error
            return back()->withErrors(['error' => $e->getMessage()], 500);
        }
    }
}
