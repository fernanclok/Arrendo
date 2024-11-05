<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'issue_date',
        'total_amount',
        'payment_status',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
