<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Contract_renewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'renewal_start_date',
        'renewal_end_date',
        'new_renewal_rental_amount',
        'status', //Pending, Approved, Rejected
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id');
    }

    public function validateStatus(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'status' => [
                'required',
                Rule::in(['Pending', 'Approved', 'Rejected']),
            ],
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
