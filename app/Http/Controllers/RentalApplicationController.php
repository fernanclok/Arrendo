<?php
namespace App\Http\Controllers;

use App\Models\Rental_application;
use App\Models\Rental_document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function lastApplicationCreated()
    {
        $last_id = DB::table('rental_applications')->latest('id')->first();
        $id = $last_id->id;
        

        return response()->json($id);
    }
    
    public function uploadDocument(Request $request)
    {
        $application_id = $this->lastApplicationCreated();

        $validator = Validator::make($request->all(), [
            'application_id' => 'required',
            'document_type' => 'required',
            'document_path' => 'required',
            'upload_date' => 'required',
            'document_status' => 'required',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de los datos',
                'error' => $validator->errors(),
                'status' => 200
            ];

            return response()->json($data, 400);
        }

        $document = DB::table('rental_documents')->insert([
            'application_id' => $application_id,
            'document_type' => $request->document_type,
            'document_path' => $request->document_path,
            'upload_date' => $request->upload_date,
            'document_status' => $request->document_status,
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'data' => $document
        ]);
    }
}

