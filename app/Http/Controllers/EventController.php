<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SocietyApprover;
use App\Models\NotificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Mail\EventApprovalRequest;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EventController extends Controller
{
    // ✅ Store a new event
    public function store(Request $request)
    {
        Log::info("📥 Event create request by user ID: " . auth()->id());

        $validated = $request->validate([
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'university'  => 'required|string',
            'faculty'     => 'nullable|string',
            'date'        => 'required|date',
            'time'        => 'required',
            'type'        => 'required|string',
            'location'    => 'nullable|string',
            'audience'    => 'required|string',
            'society'     => 'required|string',
            'position'    => 'required|string',   // creator’s position
            'approver'    => 'required|string',   // approver’s position (used to lookup email)
            'media'       => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Normalize time to HH:MM:SS
        $validated['time'] = Carbon::parse($validated['time'])->format('H:i:s');

        // Basic authorization on creator’s position
        $normalizedPosition = strtolower(str_replace([' ', '-', '_'], '', $validated['position']));
        $allowedPositions   = [
            'president', 'coeditor', 'socialmediacoordinator',
            'secretary', 'juniortreasurer', 'organizingcommittee'
        ];
        if (!in_array($normalizedPosition, $allowedPositions, true)) {
            return response()->json(['error' => 'Unauthorized position for event creation.'], 403);
        }

        // Optional media
        if ($request->hasFile('media')) {
            $validated['media_path'] = $request->file('media')->store('event_media', 'public');
        }

        $validated['status']  = 'pending';
        $validated['user_id'] = auth()->id();

        // ✅ Create event + approval token
        try {
            DB::beginTransaction();

            // Retry helps with transient SQLite locks
            $event = retry(5, fn () => Event::create($validated), 100);
            $event->approval_token = Str::random(40);
            $event->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("❌ Event creation failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Database write error.'], 500);
        }

        // ✅ Find approver by society + approver position (case-insensitive, trimmed)
        $approver = SocietyApprover::whereRaw('LOWER(TRIM(society)) = ?', [strtolower(trim($validated['society']))])
            ->whereRaw('LOWER(TRIM(position)) = ?', [strtolower(trim($validated['approver']))])
            ->first();

        // --- DIAGNOSTICS (what mail settings are actually in use) ---
        Log::info('✉️ Mail config snapshot', [
            'default'     => config('mail.default'),
            'from'        => config('mail.from'),
            'smtp.host'   => data_get(config('mail.mailers.smtp'), 'host'),
            'smtp.port'   => data_get(config('mail.mailers.smtp'), 'port'),
            'smtp.enc'    => data_get(config('mail.mailers.smtp'), 'encryption'),
            'queue_conn'  => config('queue.default'),
            'env_mailer'  => env('MAIL_MAILER'),
            'env_host'    => env('MAIL_HOST'),
            'env_port'    => env('MAIL_PORT'),
            'env_encrypt' => env('MAIL_ENCRYPTION'),
        ]);
        // ------------------------------------------------------------

        if ($approver && !empty($approver->email)) {
            // ✅ Step E: precise SMTP transport error logging
            try {
                Mail::to($approver->email)->send(new EventApprovalRequest($event->fresh()));
                Log::info("✅ Approval email sent to {$approver->email}");
            } catch (TransportExceptionInterface $e) {
                $prevMsg = $e->getPrevious() ? $e->getPrevious()->getMessage() : null;
                Log::error('❌ SMTP transport error while sending approval email', [
                    'message'  => $e->getMessage(),
                    'previous' => $prevMsg,
                ]);
            } catch (\Throwable $e) {
                Log::error('❌ Mail send failed (non-transport)', [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]);
            }
        } else {
            Log::warning("⚠️ Approver not found or email missing for society='{$validated['society']}', approver_position='{$validated['approver']}'");
        }

        // ✅ Push notification (unchanged)
        try {
            $tokens = NotificationToken::pluck('token')->toArray();

            if (!empty($tokens)) {
                Http::withHeaders([
                    'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
                    'Content-Type'  => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'registration_ids' => $tokens,
                    'notification'     => [
                        'title' => '📢 New Event: ' . $event->name,
                        'body'  => '📍 ' . $event->university . ' | ' . ($event->faculty ?? '-') .
                                   ' | 🗓 ' . Carbon::parse($event->date)->format('F j') .
                                   ' | 🕒 ' . Carbon::parse($event->time)->format('g:i A'),
                    ],
                ]);

                Log::info("📲 Push notification broadcasted to " . count($tokens) . " users");
            }
        } catch (\Throwable $e) {
            Log::error("❌ Failed to send push notification: " . $e->getMessage());
        }

        // Attach computed image URL for response
        $event->image_url = $event->media_path ? asset('storage/' . $event->media_path) : null;

        return response()->json([
            'message' => 'Event created successfully. Email and push notification attempted.',
            'event'   => $event
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            Log::error('❌ Failed to fetch approved events: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load approved events.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event  = $this->addImageUrl($event); // ✅ attach image_url
            return response()->json($event);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Failed to load event',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pastSeries(Request $request)
    {
        $name      = $request->query('name');
        $excludeId = $request->query('excludeId');
        $now       = Carbon::now();

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
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'university'  => 'required|string',
            'faculty'     => 'nullable|string',
            'date'        => 'required|date',
            'time'        => 'required',
            'type'        => 'required|string',
            'location'    => 'nullable|string',
            'audience'    => 'required|string',
            'society'     => 'required|string',
            'approver'    => 'required|string',
            'status'      => 'required|string',
        ]);

        $validated['time'] = Carbon::parse($validated['time'])->format('H:i:s');

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
                    $event->image_url = $event->media_path ? asset('storage/' . $event->media_path) : null;
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