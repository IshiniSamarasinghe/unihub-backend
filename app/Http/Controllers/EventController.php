<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SocietyApprover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'university' => 'required|string',
            'faculty' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'audience' => 'required|string',
            'society' => 'required|string',
            'position' => 'required|string',
            'approver' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ Upload media if present
        if ($request->hasFile('media')) {
            $validated['media_path'] = $request->file('media')->store('event_media', 'public');
        }

        // ✅ Default status and user
        $validated['status'] = 'pending';
        $validated['user_id'] = auth()->id() ?? null;

        // ✅ Create event and generate approval token
        $event = Event::create($validated);
        $token = Str::random(40);
        $event->approval_token = $token;
        $event->save();

        // ✅ Fetch approver WhatsApp number
        $approver = SocietyApprover::where('society', $validated['society'])
            ->where('position', $validated['approver'])
            ->first();

        if ($approver) {
    $phone = $approver->whatsapp_number;

    // Generate approval token
    $token = Str::random(40);

    // Create approval/rejection links with newlines
    $acceptLink = "http://localhost:8000/approve?token={$token}";
    $rejectLink = "http://localhost:8000/reject?token={$token}";

    // Build message body with line breaks before each link
   $message = "📢 *New Event Approval Request*\n\n"
    . "🔸 *Society:* {$validated['society']}\n"
    . "🔸 *Event:* {$validated['name']}\n"
    . "🔸 *Requested by:* {$validated['position']}\n"
    . "🔸 *Date:* {$validated['date']} at {$validated['time']}\n\n"
    . "Do you approve this event?\n"
    . "Reply with:\n"
    . "1️⃣ to *Approve*\n"
    . "2️⃣ to *Reject*";

    // Send WhatsApp message via UltraMsg
    Http::post("https://api.ultramsg.com/instance126986/messages/chat", [
        'token' => '4u4vlvyapuac2r6j',
        'to' => $phone,
        'body' => $message,
    ]);
}

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
    }
}
