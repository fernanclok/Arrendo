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
        'street',
        'number',
        'city',
        'state',
        'postal_code',
        //'rental_rate',
        'availability', //Available, Not Available
        'total_bathrooms',
        'total_rooms',
        'total_m2',
        'have_parking', // 1 = Yes, 0 = No
        'accept_mascots',
        'property_price',
        'property_details',
        'property_photos_path',
        'owner_user_id',
        'zone_id',

        'colony', 
        'half_bathrooms', 
        'surface_built', 
        'total_surface', 
        'antiquity', 
        'maintenance', 
        'state_conservation', 
        'wineries', 
        'closets', 
        'levels',
        'parking', 

        'general_features',
        'services',
        'exteriors',
        'environmentals',
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

    public function comments()
    {
        return $this->hasMany(Comment::class, 'property_id');
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


