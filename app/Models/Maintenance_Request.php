<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Maintenance_request extends Model
{
    use HasFactory;
    protected $table = 'maintenance_requests';
    protected $fillable = [
        'property_id',
        'tenant_user_id',
        'type',
        'description',
        'report_date',
        'priority', //Low, Medium, High
        'status', //Pending, In Progress, Completed
        'evidence',
        'owner_note',
        'maintenance_cost',
        'date_review',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function tenantUser()
    {
        return $this->belongsTo(User::class, 'tenant_user_id');
    }

    public function validatePriority(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'priority' => [
                'required',
                Rule::in(['Low', 'Medium', 'High']),
            ],
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    public function validateStatus(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'status' => [
                'required',
                Rule::in(['Pending', 'In Progress', 'Completed']),
            ],
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
