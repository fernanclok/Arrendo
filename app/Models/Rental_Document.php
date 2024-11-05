<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental_Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'document_type',
        'document_path',
        'upload_date',
        'document_status',
    ];

    public function rental_application()
    {
        return $this->belongsTo(Rental_Application::class);
    }
}
