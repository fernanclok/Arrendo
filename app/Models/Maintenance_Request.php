<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance_Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_id',
        'description',
        'reported_date',
        'priority',
        'status',
        'evidence',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
