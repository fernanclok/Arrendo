<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appoinment;
use App\Models\User;
use App\Models\Property;

class AppointmentController extends Controller
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

    public function getUserAppointments(Request $request)
    {
        $user = User::where('id', $request->user_id)->where('role', 'Tenant')->first();
        if (!$user) {
            return response()->json(['message' => 'User not found or is not a tenant']);
        }

        //verificar que el user existe y tiene rol de tenant
        $appoinments = Appoinment::where('user_id', $request->user_id)
            ->with('property') // Cargar la propiedad relacionada
            ->get();

        return response()->json($appoinments);
    }

    public function getOwnerRequests(Request $request)
    {
        // Verificar que el usuario existe y tiene el rol de "Owner"
        $user = User::where('id', $request->user_id)->where('role', 'Owner')->first();
        if (!$user) {
            return response()->json(['message' => 'User not found or is not an owner'], 404);
        }

        // Obtener todas las propiedades del propietario
        $properties = Property::where('owner_user_id', $request->user_id)->pluck('id');

        // Obtener todas las citas para las propiedades del propietario
        $appointments = Appoinment::whereIn('property_id', $properties)
            ->with('user') // Cargar el usuario relacionado
            ->with('property') // Cargar la propiedad relacionada
            ->get();

        return response()->json($appointments);
    }

    public function updateAppointment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required',
            'status' => 'required',
            'rejected_reason' => 'nullable|string',
        ]);

        $appointment = Appoinment::find($request->appointment_id);
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $appointment->status = $request->status;

        // Agregar la fecha y hora de confirmación si el estado es "Approved"
        if ($request->status === 'Approved') {
            $appointment->confirmation_date = now();
            $appointment->rejected_reason = null; // Limpiar el motivo de rechazo si está aprobado
        } elseif ($request->status === 'Rejected') {
            $appointment->rejected_reason = $request->rejected_reason;
            $appointment->confirmation_date = now(); // Limpiar la fecha de confirmación si está rechazado
        }   elseif ($request->status === 'Confirmed') {
            $appointment->rejected_reason = null;
            $appointment->confirmation_date = $request->confirmation_date; // Limpiar la fecha de confirmación si está rechazado
        } elseif($request->status === 'Cancelled') {
            $appointment->confirmation_date = null; // Limpiar la fecha de confirmación si está cancelado
            $appointment->rejected_reason = null; // Limpiar el motivo de rechazo si está cancelado
        } elseif($request->status === 'Applicated') {
            $appointment->confirmation_date = $request->confirmation_date; // Limpiar la fecha de confirmación si está aplicado
            $appointment->rejected_reason = null; // Limpiar el motivo de rechazo si está aplicado
        } else {
            $appointment->confirmation_date = null; // Limpiar la fecha de confirmación si no está aprobado
            $appointment->rejected_reason = null; // Limpiar el motivo de rechazo si no está rechazado
        }

        $appointment->save();

        return response()->json([
            'message' => 'Appointment updated successfully',
            'data' => $appointment
        ]);
    }
}
