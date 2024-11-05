<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract_Renewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'renewal_start_date',
        'renewal_end_date',
        'renewal_rental_amount',
        'renewal_status',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
