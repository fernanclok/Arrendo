<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractTermination extends Model
{
    use HasFactory;
    protected $table = 'contract_terminations';

    protected $fillable = [
        'contract_id',
        'reason',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
