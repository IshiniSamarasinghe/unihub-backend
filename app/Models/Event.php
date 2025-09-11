<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventMedia; // ✅ important

class Event extends Model
{
    protected $fillable = [
        'name', 'description', 'university', 'faculty', 'date',
        'time', 'type', 'location', 'audience',
        'society', 'position', 'approver', 'user_id', 'media_path'
    ];

    public function media()
    {
        return $this->hasMany(EventMedia::class);
    }

    
}