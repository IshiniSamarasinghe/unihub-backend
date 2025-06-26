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
        Log::info("📥 Event create request by user ID: " . auth()->id());

        // ✅ Step 1: Validate form data
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

        // ✅ Step 2: Normalize position and check permission
        $normalizedPosition = strtolower(str_replace([' ', '-', '_'], '', $request->position));
        $allowedNormalizedPositions = [
            'president',
            'coeditor',
            'socialmediacoordinator',
            'secretary',
            'juniortreasurer',
            'organizingcommittee'
        ];

        if (!in_array($normalizedPosition, $allowedNormalizedPositions)) {
            return response()->json(['error' => 'Unauthorized position for event creation.'], 403);
        }

        // ✅ Step 3: Upload media if present
        if ($request->hasFile('media')) {
            $validated['media_path'] = $request->file('media')->store('event_media', 'public');
        }

        // ✅ Step 4: Add status and user info
        $validated['status'] = 'pending';
        $validated['user_id'] = auth()->id() ?? null;

        // ✅ Step 5: Create event
        $event = Event::create($validated);

        // ✅ Step 6: Generate approval token and save
        $token = Str::random(40);
        $event->approval_token = $token;
        $event->save();

        // ✅ Step 7: Lookup approver using the approver field (not submitter's position)
        $validated['society'] = trim($validated['society']);
        $validated['approver'] = trim($validated['approver']);
        Log::info("🔍 Looking for approver with society: {$validated['society']} and position: {$validated['approver']}");

        $approver = SocietyApprover::whereRaw('LOWER(society) = ?', [strtolower($validated['society'])])
            ->whereRaw('LOWER(position) = ?', [strtolower($validated['approver'])])
            ->first();

        // ✅ Step 8: Send email if approver found
        if ($approver && !empty($approver->email)) {
            try {
                Mail::to($approver->email)->send(new EventApprovalRequest($event));
                Log::info("📧 Email sent to approver: {$approver->email}");
            } catch (\Exception $e) {
                Log::error("❌ Failed to send email: " . $e->getMessage());
            }
        } else {
            Log::error("❌ Approver not found or email missing for: {$validated['society']} - {$validated['approver']}");
        }

        return response()->json([
            'message' => 'Event created successfully and approval email attempted.',
            'event' => $event
        ], 201);
    }
}
