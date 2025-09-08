<?php

// app/Models/SocietyApprover.php
namespace App\Models;

// app/Models/SocietyApprover.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocietyApprover extends Model
{
    protected $table = 'society_approvers';
    protected $fillable = ['society','position','whatsapp_number','email'];
}



