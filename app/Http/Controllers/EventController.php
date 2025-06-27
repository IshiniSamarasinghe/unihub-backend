<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SocietyApprover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\EventApprovalRequest;

class EventController extends Controller
{
    public function store(Request $request)
    {
        Log::info("📥 Event create request by user ID: " . auth()->id());

        // ✅ Step 1: Validate input
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

        // ✅ Step 2: Check allowed positions
        $normalizedPosition = strtolower(str_replace([' ', '-', '_'], '', $validated['position']));
        $allowedPositions = [
            'president', 'coeditor', 'socialmediacoordinator',
            'secretary', 'juniortreasurer', 'organizingcommittee'
        ];

        if (!in_array($normalizedPosition, $allowedPositions)) {
            return response()->json(['error' => 'Unauthorized position for event creation.'], 403);
        }

        // ✅ Step 3: Handle media upload
        if ($request->hasFile('media')) {
            $validated['media_path'] = $request->file('media')->store('event_media', 'public');
        }

        // ✅ Step 4: Append default fields
        $validated['status'] = 'pending';
        $validated['user_id'] = auth()->id() ?? null;

        // ✅ Step 5: Retry-safe DB insert
        try {
            DB::beginTransaction();

            $event = retry(5, function () use ($validated) {
                return Event::create($validated);
            }, 100);

            $event->approval_token = Str::random(40);
            $event->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Event creation failed: " . $e->getMessage());
            return response()->json(['error' => 'Database write error. Please try again.'], 500);
        }

        // ✅ Step 6: Lookup approver
        $approver = SocietyApprover::whereRaw('LOWER(society) = ?', [strtolower(trim($validated['society']))])
            ->whereRaw('LOWER(position) = ?', [strtolower(trim($validated['approver']))])
            ->first();

        // ✅ Step 7: Send email to approver
        if ($approver && !empty($approver->email)) {
            try {
                // Recommended: use queue
                // Mail::to($approver->email)->queue(new EventApprovalRequest($event));
                Mail::to($approver->email)->send(new EventApprovalRequest($event->fresh()));

                Log::info("📧 Email sent to approver: {$approver->email}");
            } catch (\Exception $e) {
                Log::error("❌ Failed to send approval email: " . $e->getMessage());
            }
        } else {
            Log::warning("⚠️ Approver not found or missing email: {$validated['society']} - {$validated['approver']}");
        }

        return response()->json([
            'message' => 'Event created successfully and email sent (if approver exists).',
            'event' => $event
        ], 201);
    }
}
