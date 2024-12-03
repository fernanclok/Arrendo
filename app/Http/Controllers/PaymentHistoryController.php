<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment_history;

class PaymentHistoryController extends Controller
{
    public function getPaymentHistoriesByOwner(Request $request)
    {
        $ownerUserId = $request->user_id;

        $paymentHistories = Payment_history::whereHas('invoice.contract', function ($query) use ($ownerUserId) {
            $query->where('owner_user_id', $ownerUserId);
        })->get();
    
        return response()->json($paymentHistories);
    }
}