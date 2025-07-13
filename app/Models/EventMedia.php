<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMedia extends Model
{
    protected $fillable = [
        'event_id', 'type', 'file_path'
    ];

    /**
     * Relationship: Each media belongs to an event.
     */
    public function event()
    {
        return $this->belongsTo(EventModel::class, 'event_id');
        // If your event model is named EventModel.php, use it here.
        // If you rename it to Event.php later, change this to Event::class
    }
}
