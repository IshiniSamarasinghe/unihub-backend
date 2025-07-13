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
            'message' => 'Event created successfully. Email and push notification sent.',
            'event' => $event
        ], 201);
    }

    public function all()
    {
        try {
            $events = Event::where('status', 'approved')
                ->whereNotNull('media_path')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($event) => $this->addImageUrl($event));

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('❌ Failed to fetch approved events: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load events.'], 500);
        }
    }

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

    public function show($id)
{
    try {
        $event = Event::findOrFail($id);
        $event = $this->addImageUrl($event); // ✅ attach image_url
        return response()->json($event);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'Failed to load event',
            'message' => $e->getMessage()
        ], 500);
    }
}



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

    private function addImageUrl($event)
    {
        $event->image_url = $event->media_path ? asset('storage/' . $event->media_path) : null;
        return $event;
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully.']);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

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
            'approver' => 'required|string',
            'status' => 'required|string',
        ]);

        $validated['time'] = \Carbon\Carbon::parse($validated['time'])->format('H:i:s');

        $event->update($validated);

        return response()->json(['message' => 'Event updated successfully.']);
    }

    // ✅ For MyEvents (super user)
public function mine()
{
    try {
        $userId = auth()->id();
        Log::info("✅ MyEvents triggered by user ID: $userId");

        $events = Event::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                // ✅ Attach full URL
                $event->image_url = $event->media_path
                    ? asset('storage/' . $event->media_path)
                    : null;
                return $event;
            });

        return response()->json($events);

    } catch (\Throwable $e) {
        Log::error('❌ Error in EventController@mine: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}
}
