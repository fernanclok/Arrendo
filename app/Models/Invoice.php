<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'issue_date',
        'total_amount',
        'evidence_path',
        'payment_status', //Paid, Pending
        'invoice_status', // Active, Inactive
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function paymentHistory()
    {
        return $this->hasOne(Payment_history::class, 'invoice_id');
    }

    public function validatePaymentStatus(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'payment_status' => [
                'required',
                Rule::in(['Paid', 'Pending']),
            ],
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    public function validateInvoiceStatus(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'invoice_status' => [
                'required',
                Rule::in(['Active', 'Inactive']),
            ],
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
