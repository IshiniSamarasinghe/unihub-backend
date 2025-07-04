<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'event_id',
        'token',
        'status',
        'error_message',
    ];
}
