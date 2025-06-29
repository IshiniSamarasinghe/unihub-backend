<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocietyApprover extends Model
{
    protected $fillable = ['society', 'position', 'whatsapp_number', 'email'];
}

