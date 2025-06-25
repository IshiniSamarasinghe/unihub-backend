<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SocietyApprover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\EventApprovalRequest;
use Illuminate\Support\Facades\Log;

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
            'media' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ Upload media if present
        if ($request->hasFile('media')) {
            $validated['media_path'] = $request->file('media')->store('event_media', 'public');
        }

        // ✅ Add status and user info
        $validated['status'] = 'pending';
        $validated['user_id'] = auth()->id() ?? null;

        // ✅ Create event
        $event = Event::create($validated);

        // ✅ Generate approval token and save it
        $token = Str::random(40);
        $event->approval_token = $token;
        $event->save();

        // ✅ Sanitize & log for debugging
        $validated['society'] = trim($validated['society']);
        $validated['position'] = trim($validated['position']);
        Log::info("🔍 Looking for approver with society: {$validated['society']} and position: {$validated['position']}");

        // ✅ Find approver (case-insensitive lookup)
        $approver = SocietyApprover::whereRaw('LOWER(society) = ?', [strtolower($validated['society'])])
            ->whereRaw('LOWER(position) = ?', [strtolower($validated['position'])])
            ->first();

        if ($approver && !empty($approver->email)) {
            try {
                Mail::to($approver->email)->send(new EventApprovalRequest($event));
                Log::info("📧 Email sent to approver: {$approver->email}");
            } catch (\Exception $e) {
                Log::error("❌ Failed to send email: " . $e->getMessage());
            }
        } else {
            Log::error("❌ Approver not found or email is missing for: {$validated['society']} - {$validated['position']}");
        }

        return response()->json([
            'message' => 'Event created successfully and approval email attempted.',
            'event' => $event
        ], 201);
    }
}
