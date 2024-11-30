<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;


class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();
        return response()->json($invoices);
    }

    // public function generateInvoices($contractId)
    // {
    //     try {
    //         // Obtener el contrato
    //         $contract = Contract::findOrFail($contractId);

    //         $startDate = Carbon::parse($contract->start_date);
    //         $endDate = Carbon::parse($contract->end_date);
    //         $totalAmount = $contract->rental_amount;

    //         $invoices = [];
    //         while ($startDate <= $endDate) {
    //             // Crear una nueva factura para cada mes
    //             $invoice = new Invoice([
    //                 'contract_id' => $contractId,
    //                 'issue_date' => $startDate->format('Y-m-d'),
    //                 'total_amount' => $totalAmount,
    //                 'payment_status' => 'Pending',
    //             ]);

    //             // Guardar la factura
    //             $invoice->save();
    //             $invoices[] = $invoice;

    //             // Incrementar la fecha al siguiente mes
    //             $startDate->addMonth();
    //         }

    //         return response()->json($invoices, 201);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Error generating invoices', 'details' => $e->getMessage()], 500);
    //     }
    // }

    public function InvoicePaid($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->payment_status = 'Paid';
            $invoice->save();

            return response()->json(['message' => 'Invoice marked as paid successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating payment status', 'details' => $e->getMessage()], 500);
        }
    }


    public function MyInvoices()
    {
        try {

            $tenantUserId = 3;

            $history = DB::table('users as u')
                ->join('contracts as c', 'u.id', '=', 'c.tenant_user_id')
                ->join('invoices as i', 'c.id', '=', 'i.contract_id')
                ->select(
                    'u.id as tenant_user_id',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as tenant_name"),
                    'c.id as contract_id',
                    'i.id as invoice_id',
                    'i.issue_date as invoice_date',
                    'i.total_amount as invoice_total',
                    'i.payment_status'
                )
                ->where('c.tenant_user_id', $tenantUserId)
                ->orderBy('i.issue_date', 'DESC')
                ->get();

            return response()->json($history, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving payment history', 'details' => $e->getMessage()], 500);
        }
    }
}
