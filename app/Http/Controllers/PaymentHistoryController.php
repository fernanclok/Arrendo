<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment_history;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;

class PaymentHistoryController extends Controller
{
    public function getPaymentHistoriesByOwner(Request $request)
    {
        $ownerUserId = $request->user_id;

        $paymentHistories = Payment_history::with('invoice.contract.tenantUser')
            ->whereHas('invoice.contract', function ($query) use ($ownerUserId) {
                $query->where('owner_user_id', $ownerUserId);
            })->get();

        $result = $paymentHistories->map(function ($paymentHistory) {
            return [
                'payment_history' => $paymentHistory,
                'tenant' => $paymentHistory->invoice->contract->tenantUser
            ];
        });

        return response()->json($result);
    }

    public function getPaymentHistoriesByTenant(Request $request)
    {
        $tenantUserId = $request->user_id;

        $paymentHistories = Payment_history::whereHas('invoice.contract', function ($query) use ($tenantUserId) {
            $query->where('tenant_user_id', $tenantUserId)->orderby('created_at', 'desc');
        })->get();

        return response()->json($paymentHistories);
    }
    public function getUnpaidInvoices(Request $request)
    {
        $ownerUserId = $request->user_id;

        $unpaidInvoices = Invoice::whereHas('contract', function ($query) use ($ownerUserId) {
            $query->where('owner_user_id', $ownerUserId);
        })->where('payment_status', 'Pending')
            ->where('issue_date', '<', now())
            ->get();
        return response()->json($unpaidInvoices);
    }

    public function getTenantsforHistory(Request $request)
    {   
        $ownerUserId = $request->user_id;

        $tenants = Payment_history::whereHas('invoice.contract', function ($query) use ($ownerUserId) {
            $query->where('owner_user_id', $ownerUserId);
        })->with(['invoice.contract.tenantUser' => function ($query) {
            $query->select('id', 'first_name', 'last_name');
        }])->get()->pluck('invoice.contract.tenantUser')->unique();

        return response()->json($tenants);
    }
}
