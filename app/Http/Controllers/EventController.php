<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SocietyApprover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Mail\EventApprovalRequest;

class EventController extends Controller
{
    // ✅ Store a new event
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

    // ✅ Normalize time to 24-hour format (fixes scheduling issue)
    $validated['time'] = \Carbon\Carbon::parse($validated['time'])->format('H:i:s');

    $normalizedPosition = strtolower(str_replace([' ', '-', '_'], '', $validated['position']));
    $allowedPositions = [
        'president', 'coeditor', 'socialmediacoordinator',
        'secretary', 'juniortreasurer', 'organizingcommittee'
    ];

    if (!in_array($normalizedPosition, $allowedPositions)) {
        return response()->json(['error' => 'Unauthorized position for event creation.'], 403);
    }

    if ($request->hasFile('media')) {
        $validated['media_path'] = $request->file('media')->store('event_media', 'public');
    }

    $validated['status'] = 'pending';
    $validated['user_id'] = auth()->id();

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

    // ✅ Email to approver
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

    // ✅ Push Notification to all users
    try {
        $tokens = \App\Models\NotificationToken::pluck('token')->toArray();

        if (!empty($tokens)) {
            \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'registration_ids' => $tokens,
                'notification' => [
                    'title' => '📢 New Event: ' . $event->name,
                    'body' => '📍 ' . $event->university . ' | ' . ($event->faculty ?? '-') . ' | 🕒 ' . \Carbon\Carbon::parse($event->date)->format('F j, g:i A'),
                ],
            ]);

            Log::info("📲 Push notification broadcasted to " . count($tokens) . " users");
        }
    } catch (\Exception $e) {
        Log::error("❌ Failed to send push notification: " . $e->getMessage());
    }

    $event->image_url = $event->media_path ? asset('storage/' . $event->media_path) : null;

    return response()->json([
        'message' => 'Event created successfully and email/push sent.',
        'event' => $event
    ], 201);
}


    // ✅ Fetch all events
    public function all()
    {
        try {
            $events = Event::whereNotNull('media_path')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($event) => $this->addImageUrl($event));

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('❌ Failed to fetch all events: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load all events.'], 500);
        }
    }

    // ✅ Fetch pending events
    public function pending()
    {
        try {
            $events = Event::where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($event) => $this->addImageUrl($event));

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('❌ Failed to fetch pending events: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load pending events.'], 500);
        }
    }

    // ✅ Fetch rejected events
    public function rejected()
    {
        try {
            $events = Event::where('status', 'rejected')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($event) => $this->addImageUrl($event));

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('❌ Failed to fetch rejected events: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load rejected events.'], 500);
        }
    }

    // ✅ Fetch approved events
    public function approved()
    {
        try {
            $events = Event::where('status', 'approved')
                ->orderBy('date', 'asc')
                ->get()
                ->map(fn($event) => $this->addImageUrl($event));

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('❌ Failed to fetch approved events: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load approved events.'], 500);
        }
    }

    // ✅ Fetch single event by ID (for EventDetails)
    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->image_url = $event->media_path ? asset('storage/' . $event->media_path) : null;

            return response()->json($event);
        } catch (\Exception $e) {
            Log::error("❌ Failed to fetch event by ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Event not found.'], 404);
        }
    }

    // ✅ Fetch past events that belong to the same series (like Innovista)
    public function pastSeries(Request $request)
{
    $name = $request->query('name');
    $excludeId = $request->query('excludeId');
    $now = Carbon::now();

    if (!$name) {
        return response()->json([]);
    }

    $query = Event::where('name', 'LIKE', "%$name%")
        ->where('status', 'approved')
        ->whereRaw("datetime(date || ' ' || time) < ?", [$now]);

    if ($excludeId) {
        $query->where('id', '!=', $excludeId);
    }

    $events = $query->orderBy('date', 'desc')->get()
        ->map(fn($event) => $this->addImageUrl($event));

    return response()->json($events);
}

    // ✅ Helper to add image URL
    private function addImageUrl($event)
    {
        $event->image_url = $event->media_path ? asset('storage/' . $event->media_path) : null;
        return $event;
    }
}
