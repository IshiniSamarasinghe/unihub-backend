<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
   protected $fillable = [
    'name',
    'description',
    'university',
    'faculty',
    'date',
    'time',
    'type',
    'location',
    'audience',
    'society',
    'position',
    'approver',
    'status',
    'media_path',
    'user_id',
    'approval_token',
];
}
