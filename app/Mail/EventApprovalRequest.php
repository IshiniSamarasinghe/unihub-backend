<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class EventApprovalRequest extends Mailable

{
    use Queueable, SerializesModels;

    public $event;

    /**
     * Create a new message instance.
     */
    public function __construct(Event $event)
    {
        $this->event = $event;

        // 🧪 Log event data to verify fields
        Log::info('📨 Sending EventApprovalRequest mail with data:', $event->toArray());
    }

    /**
     * Build the message.
     */
    public function build()
    {
       return $this->subject('📩 Event Approval Request - ' . ($this->event->name ?? 'New Event'))
            ->markdown('emails.event_approval')
            ->with([
                'event' => $this->event,
            ]);


    }
}
