<?php

namespace App\Http\Controllers;

use App\Models\Rental_application;
use App\Models\Rental_document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentalApplicationController extends Controller
{
    public function index()
    {
        $applications = Rental_application::with('tenantUser', 'property')->get();

        return response()->json($applications);
    }

    public function approve($id)
    {
        try {
            $application = Rental_application::findOrFail($id);
            $propertyId = $application->property_id;

            // Actualizar el estado de la solicitud aprobada
            $application->validateStatus(['status' => 'Approved']);
            $application->status = 'Approved';
            $application->save();

            // Rechazar automáticamente las demás solicitudes para la misma propiedad
            Rental_application::where('property_id', $propertyId)
                ->where('id', '!=', $id)
                ->update(['status' => 'Rejected']);

            // Marcar la propiedad como no disponible
            $property = $application->property;
            $property->availability = 'Not Available';
            $property->save();

            return response()->json([
                'message' => 'Application approved and others rejected.',
                'updatedSolicitantes' => Rental_application::with('tenantUser', 'property')->get(), // Regresar lista actualizada
                'property' => $property // Devolver el estado de la propiedad actualizada
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Application not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Invalid status provided.'], 422);
        }
    }

    public function reject($id)
    {
        try {
            $application = Rental_application::findOrFail($id);
            $application->validateStatus(['status' => 'Rejected']);
            $application->status = 'Rejected';
            $application->save();

            return response()->json(['message' => 'Application rejected.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Application not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Invalid status provided.'], 422);
        }
    }


    public function AplicationDocuments()
    {
        $documents = Rental_document::all();
    }

    // Insert contract information
    public function storeAppDocuments(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validator = Validator::make($request->all(), [
                'application_id' => 'required',
                'application_files.*' => 'required|file|mimes:pdf,png,jpg,jpeg'
            ]);

            if ($validator->fails()) {
                $data = [
                    'message' => 'Error en la validacion de los datos',
                    'error' => $validator->errors(),
                    'status' => 200
                ];

                return response()->json($data, 400);
            }

            $validatedData = $validator->validated();

            // Crear un nuevo contrato
            $app_document = new Rental_document();
            $app_document->application_id = $validatedData['application_id'];


            if ($request->hasFile('application_files')) {
                $files = [];
                foreach ($request->file('application_files') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = now()->format('YmdHis');

                    // Reemplazar espacios por guiones bajos o quitarlos
                    $sanitizedOriginalName = preg_replace('/\s+/', '_', $originalName);
                    $newName = "{$sanitizedOriginalName}_{$timestamp}.{$extension}";
                    $destinationPath = public_path('application_files');

                    // Crear la carpeta si no existe
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    $file->move($destinationPath, $newName);
                    $files[] = "application_files/{$newName}";
                }
                $app_document->document_path = json_encode($files);
            }

            $app_document->save();

            // Devolver una respuesta JSON de éxito
            return response()->json(['message' => 'Documents uploaded succesfully', 'Documents' => $app_document], 201);
        } catch (\Exception $e) {
            // Devolver una respuesta JSON de error
            return response()->json(['success' => false, 'message' => $e,], 500);
        }
    }
}
