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
    /**
     * Store a new event submitted by a user.
     */
    public function store(Request $request)
    {
        Log::info("📥 Event create request by user ID: " . auth()->id());

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

        // ✅ Role validation
        $normalizedPosition = strtolower(str_replace([' ', '-', '_'], '', $validated['position']));
        $allowedPositions = [
            'president', 'coeditor', 'socialmediacoordinator',
            'secretary', 'juniortreasurer', 'organizingcommittee'
        ];

        if (!in_array($normalizedPosition, $allowedPositions)) {
            return response()->json(['error' => 'Unauthorized position for event creation.'], 403);
        }

        // ✅ Media handling
        if ($request->hasFile('media')) {
            $validated['media_path'] = $request->file('media')->store('event_media', 'public');
        }

        $validated['status'] = 'pending';
        $validated['user_id'] = auth()->id();

        // ✅ Safe DB insert with transaction
        try {
            DB::beginTransaction();

            $event = retry(5, fn () => Event::create($validated), 100);
            $event->approval_token = Str::random(40);
            $event->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Event creation failed: " . $e->getMessage());
            return response()->json(['error' => 'Database write error.'], 500);
        }

        // ✅ Approver email lookup
        $approver = SocietyApprover::whereRaw('LOWER(society) = ?', [strtolower(trim($validated['society']))])
            ->whereRaw('LOWER(position) = ?', [strtolower(trim($validated['approver']))])
            ->first();

        if ($approver && !empty($approver->email)) {
            try {
                Mail::to($approver->email)->send(new EventApprovalRequest($event->fresh()));
                Log::info("📧 Email sent to approver: {$approver->email}");
            } catch (\Exception $e) {
                Log::error("❌ Failed to send email: " . $e->getMessage());
            }
        } else {
            Log::warning("⚠️ Approver not found or email missing for: {$validated['society']} - {$validated['approver']}");
        }

        return response()->json([
            'message' => 'Event created successfully and email sent (if approver exists).',
            'event' => $event
        ], 201);
    }

    /**
     * Fetch all pending events for the admin panel.
     */
    public function pending()
    {
        try {
            $events = Event::where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($events); // ✅ plain array instead of ['events' => $events]
        } catch (\Exception $e) {
            Log::error('❌ Failed to fetch pending events: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load pending events.'], 500);
        }
    }

    /**
 * Fetch all events regardless of status.
 */
public function all()
{
    try {
        $events = Event::orderBy('created_at', 'desc')->get();
        return response()->json($events); // ✅ return plain array
    } catch (\Exception $e) {
        Log::error('❌ Failed to fetch all events: ' . $e->getMessage());
        return response()->json(['error' => 'Could not load all events.'], 500);
    }
}

public function rejected()
{
    $rejectedEvents = Event::where('status', 'rejected')->orderBy('created_at', 'desc')->get();
    return response()->json($rejectedEvents);
}



}
