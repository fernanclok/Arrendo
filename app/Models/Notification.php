<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'notification_type',
        'message',
        'sent_date',
        'read_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
