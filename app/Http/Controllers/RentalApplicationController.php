<?php

namespace App\Http\Controllers;

use App\Models\Rental_application;
use App\Models\Rental_document;
use App\Models\User;
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
        $application =  Rental_application::findOrFail($id);
        $application->status = 'Approved';
        $application->save();

        return response()->json(['message' => 'Application approved.']);
    }

    public function reject($id)
    {
        $application = Rental_Application::findOrFail($id);
        $application->status = 'Rejected';
        $application->save();

        return response()->json(['message' => 'Application rejected.']);
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

    public function passDocuments(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'application_id' => 'required|integer|exists:rental_applications,id',
            'document_path' => 'required|string'
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'error' => $validatedData->errors(),
                'status' => 400
            ], 400);
        }

        $passDocuments = new Rental_document();
        $passDocuments->application_id = $request->input('application_id');
        $passDocuments->document_path = $request->input('document_path');
        $passDocuments->save();

        return response()->json([
            'message' => 'Documentos enviados con éxito',
            'document' => $passDocuments,
            'status' => 200
        ], 200);
    }

    public function updateUserDocuments(Request $request)
    {
        // Validar los datos del request
        $validatedData = Validator::make($request->all(), [
            'tenant_user_id' => 'required|integer|exists:users,id', // Asegura que el ID de usuario existe
            'document_path' => 'required|string',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'error' => $validatedData->errors(),
                'status' => 400
            ], 400);
        }

        // Buscar al usuario por ID
        $user = User::find($request->tenant_user_id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
                'status' => 404
            ], 404);
        }

        // Actualizar el campo document_path
        $user->document_path = $request->document_path;

        // Guardar los cambios en la base de datos
        if ($user->save()) {
            return response()->json([
                'message' => 'Document path actualizado con éxito',
                'status' => 200
            ], 200);
        }

        // Manejar errores en caso de que el guardado falle
        return response()->json([
            'message' => 'Error al actualizar el document path',
            'status' => 500
        ], 500);
    }

    public function applicationsMadeByUser(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'tenant_user_id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'error' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Obtener el ID del usuario
        $tenantUserId = $request->input('tenant_user_id');

        // Consultar las aplicaciones realizadas por el usuario
        $applications = Rental_application::with('property') // Incluir información de la propiedad
            ->where('tenant_user_id', $tenantUserId)
            ->get();

        // Retornar los resultados
        return response()->json([
            'message' => 'Aplicaciones encontradas con éxito',
            'applications' => $applications,
            'status' => 200
        ], 200);
    }
}
