<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental_document extends Model
{
    use HasFactory;

    protected $table = 'rental_documents_application';

    protected $fillable = [
        'application_id',
        'document_path',
    ];

    public function application()
    {
        return $this->belongsTo(Rental_application::class, 'id');
    }
}
