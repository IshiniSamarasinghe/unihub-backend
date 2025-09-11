<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    protected $fillable = [
    'title',
    'faculty',
    'description',
    'price',
    'details',
    'end_date',   // ✅ add this
    'image_path',
];

}
