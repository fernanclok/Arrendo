<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment_history;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{

    /*public function index()
    {
        $invoices = Invoice::all();
        return response()->json($invoices);
    }*/

    public function index(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        // Evidencia del path
        $invoices = Invoice::whereYear('issue_date', $year)
            ->whereMonth('issue_date', $month)
            ->where('payment_status', 'Pending')
            ->where('invoice_status', 'Active')
            ->with([
                'contract' => function ($query) {
                    $query->select('id', 'contract_code');
                }
            ])
            ->select('id', 'issue_date', 'total_amount', 'payment_status', 'contract_id', 'evidence_path')
            ->orderBy('issue_date', 'asc')
            ->get();

        return response()->json($invoices);
    }

    public function InvoicePaid($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->payment_status = 'Paid';
            $invoice->save();

            //si el pago se marca como pagado, entonces se debe agregar a la tabla payment_histories
            $paymentHistory = new Payment_history([
                'invoice_id' => $id,
                'payment_date' => now(),
                'amount_paid' => $invoice->total_amount,
            ]);

            $paymentHistory->save();

            return response()->json(['message' => 'Invoice marked as paid successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating payment status', 'details' => $e->getMessage()], 500);
        }
    }


    public function MyInvoices(Request $request)
    {
        try {

            $history = DB::table('users as u')
                ->join('contracts as c', 'u.id', '=', 'c.tenant_user_id')
                ->join('invoices as i', 'c.id', '=', 'i.contract_id')
                ->select(
                    'u.id as tenant_user_id',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as tenant_name"),
                    'i.id as invoice_id',
                    'c.id as contract_id',
                    'c.contract_code as contract_code',
                    'i.id as invoice_id',
                    'i.created_at as invoice_date',
                    'i.issue_date as issue_date',
                    'i.evidence_path as evidence',
                    'i.total_amount as invoice_total',
                    'i.payment_status'
                )
                ->where('c.tenant_user_id', $request->user_id)
                ->where('i.invoice_status', 'Active')
                ->where('i.payment_status', 'Paid')
                ->orderBy('i.issue_date', 'asc')
                ->get();

            return response()->json($history);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Error retrieving payment history', 'details' => $e->getMessage()], 500);
        }
    }


    public function generatePDF($id)
    {
        try {
            // Obtener la factura
            $invoice = Invoice::findOrFail($id);

            // Preparar los datos para la vista
            $data = [
                'invoice' => $invoice,
            ];

            // Generar el PDF usando una vista específicayvdh\DomPDF\Facade\Pd
            $pdf = Pdf::loadView('pdf.invoice', $data);

            // Descargar el PDF
            return $pdf->download("invoice-{$id}.pdf");
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generating PDF', 'details' => $e->getMessage()], 500);
        }
    }

    // function to update the evidence path of the invoice
    public function updateEvidence(Request $request, $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            if ($request->hasFile('evidence_file')) {
                $files = [];
                foreach ($request->file('evidence_file') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = now()->format('YmdHis');

                    // Reemplazar espacios por guiones bajos o quitarlos
                    $sanitizedOriginalName = preg_replace('/\s+/', '_', $originalName);
                    $newName = "{$sanitizedOriginalName}_{$timestamp}.{$extension}";
                    $destinationPath = public_path('evidence_file');

                    // Crear la carpeta si no existe
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    $file->move($destinationPath, $newName);
                    $files[] = "evidence_file/{$newName}";
                }
                $invoice->evidence_path = json_encode($files);
            }

            $invoice->save();

            return response()->json(['message' => 'Evidence path updated successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating evidence path', 'details' => $e->getMessage()], 500);
        }
    }
}
