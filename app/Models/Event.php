<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
   protected $fillable = [
    'name', 'description', 'university', 'faculty', 'date', 'time',
    'type', 'location', 'audience', 'society', 'position', 'approver',
    'media_path', 'status', 'user_id'
];
}
