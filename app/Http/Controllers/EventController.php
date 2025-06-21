<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SocietyApprover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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

    if ($request->hasFile('media')) {
        $validated['media_path'] = $request->file('media')->store('event_media', 'public');
    }

    $validated['status'] = 'pending';
    $validated['user_id'] = auth()->id() ?? null;

    $event = Event::create($validated);

    // ✅ Fetch WhatsApp number from database
    $approver = SocietyApprover::where('society', $validated['society'])
                ->where('position', $validated['approver'])
                ->first();

    if ($approver) {
        $phone = $approver->whatsapp_number;
        $message = "{$validated['position']}, in {$validated['society']} is going to share an event in UniHub. Are you approving this or not?";

        // ✅ Send WhatsApp message via UltraMsg
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
