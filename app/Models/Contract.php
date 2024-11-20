<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_user_id',
        'start_date',
        'end_date',
        'rental_amount',
        'owner_user_id',
        'status', //Active, Pending Renewal, Terminated
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'id');
    }

    public function tenantUser()
    {
        return $this->belongsTo(User::class, 'id');
    }
    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
