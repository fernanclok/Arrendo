<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Property extends Model
{
    use HasFactory;
    protected $table = 'properties';
    protected $fillable = [
        'name',
        'address',
        'rental_rate',
        'availability', //Available, Not Available
        'total_bathrooms',
        'total_rooms',
        'total_m2',
        'have_parking', // 1 = Yes, 0 = No
        'property_price',
        'owner_user_id',
        'zone_id',
    ];

    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
      // Relación: una propiedad tiene muchos contratos
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'property_id'); // 'property_id' es la clave foránea
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function validateAvailability(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'availability' => [
                'required',
                Rule::in(['Available', 'Not Available']),
            ],
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}


