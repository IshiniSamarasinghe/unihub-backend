<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
   // app/Models/Society.php
protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_url',
        'logo_path',
        'join_link',
        'university_id',
        'is_active',
        'registration_open',
        'registration_opens_at',
        'registration_closes_at',
        // add any other columns you need to mass-assign
    ];


    /**
     * Each society belongs to one university.
     */
    public function university()
    {
        return $this->belongsTo(University::class);
    }
}
