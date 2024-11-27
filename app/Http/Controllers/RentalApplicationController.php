<?php
namespace App\Http\Controllers;

use App\Models\Rental_application;
use Illuminate\Http\Request;

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
}

