<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    protected $fillable = [
    'title', 'faculty', 'description', 'price', 'details', 'image_path'
];

}
