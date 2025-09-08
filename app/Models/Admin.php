<?php

// app/Models/Admin.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $guard = 'admin';

    protected $fillable = ['name', 'email', 'password', 'avatar_path',];

    protected $hidden = ['password'];
}

