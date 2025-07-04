<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NotificationToken extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'user_id'];

    /**
     * Relationship: Each token belongs to one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
