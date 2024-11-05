<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'rental_rate',
        'availability',
        'owner_id',
        'zone_id',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
