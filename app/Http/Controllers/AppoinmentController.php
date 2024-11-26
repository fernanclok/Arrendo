<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appoinment;

class AppoinmentController extends Controller
{
    public function createAppoinment(Request $request)
    {
        $request->validate([
            'property_id' => 'required',
            'tenant_user_id' => 'required',
            'requested_date' => 'required',
            'status' => 'required', 
        ]);

        $appoinment = new Appoinment();
        $appoinment->property_id = $request->property_id;
        $appoinment->user_id = $request->tenant_user_id;
        $appoinment->requested_date = $request->requested_date;
        $appoinment->status = $request->status;
        $appoinment->save();

        return response()->json([
            'message' => 'Appoinment created successfully',
            'data' => $appoinment
        ]);
    }
}
