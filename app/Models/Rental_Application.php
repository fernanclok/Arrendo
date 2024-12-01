<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Rental_application extends Model
{
    use HasFactory;

    protected $table = "rental_applications";

    protected $fillable = [
        'property_id',
        'tenant_user_id',
        'application_date',
        'status', //Pending, Approved, Rejected
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id'); // Usar 'property_id' como clave foránea
    }
    
    public function tenantUser()
    {
        return $this->belongsTo(User::class, 'tenant_user_id'); // Usar 'tenant_user_id' como clave foránea
    }
    
    

    public function validateStatus(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'status' => [
                'required',
                Rule::in(['Pending', 'Approved', 'Rejected']),
            ],
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
