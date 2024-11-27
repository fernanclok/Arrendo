<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    protected $table = 'contracts';

    protected $fillable = [
        'property_id',
        'tenant_user_id',
        'start_date',
        'end_date',
        'rental_amount',
        'contract_file',
        'owner_user_id',
        'status', //Active, Pending Renewal, Terminated
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenantUser()
    {
        return $this->belongsTo(User::class, 'tenant_user_id');
    }
    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
