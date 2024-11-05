<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property_Characteristic extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'characteristic_id',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function characteristic()
    {
        return $this->belongsTo(Characteristic::class);
    }
}
