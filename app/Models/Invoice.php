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
        'payment_status', //Paid, Pending
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id');
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
}
