<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Carbon\Carbon;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocietyApproverController;
use App\Http\Controllers\FirebaseNotificationController;
use App\Http\Controllers\UserNotificationPreferenceController;
use App\Http\Controllers\StoreItemController;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\EventMediaController;
use App\Http\Controllers\NotificationTokenController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/signin', [LoginController::class, 'login']);

/*
|--------------------------------------------------------------------------
| FCM Routes
|--------------------------------------------------------------------------
*/
Route::post('/save-token', [\App\Http\Controllers\NotificationTokenController::class, 'store']);

Route::post('/test-fcm', [FirebaseNotificationController::class, 'testNotification']);
Route::post('/send-notification', [FirebaseNotificationController::class, 'send']); // optional
Route::post('/broadcast-notification', [FirebaseNotificationController::class, 'broadcast']);

// Events (public reads)
Route::get('/events/approved', [EventController::class, 'approved']);
Route::get('/events/all', [EventController::class, 'all']);
Route::get('/events/pending', [EventController::class, 'pending']);
Route::get('/events/rejected', [EventController::class, 'rejected']);
Route::get('/events/past-series', [EventController::class, 'pastSeries']);

// Store (public)
Route::get('/store-items', [StoreItemController::class, 'index']);
Route::post('/store-items', [StoreItemController::class, 'store']);
Route::delete('/store-items/{id}', [StoreItemController::class, 'destroy']);
Route::put('/store-items/{id}', [StoreItemController::class, 'update']);

/*
|--------------------------------------------------------------------------
| Society Approvers
|--------------------------------------------------------------------------
*/
Route::get('/society-approvers', [SocietyApproverController::class, 'index']);
Route::get('/approvers', [SocietyApproverController::class, 'index']); // Backward compatibility

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard-metrics', [DashboardController::class, 'metrics']);

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Events (mutations + mine + media)
    Route::post('/events', [EventController::class, 'store']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::get('/events/mine', [EventController::class, 'mine']);
    Route::post('/event-media/upload', [EventMediaController::class, 'upload']);
    Route::get('/event-media/{eventId}', [EventMediaController::class, 'forEvent']);
    Route::get('/events/{id}', [EventController::class, 'show']);

    Route::get('/events/upcoming', function () {
        $now = Carbon::now('Asia/Colombo')->format('Y-m-d H:i:s'); // Ensure the correct timezone
        Log::info("🕒 [Upcoming Events] Current Time: " . $now);

        $normalizedTime = "
            CASE
                WHEN time IS NULL OR time = '' THEN '23:59:59'
                WHEN instr(time, ':') = 0 THEN time || ':00:00'
                WHEN length(time) = 4 AND instr(time, ':') = 2 THEN '0' || time || ':00'
                WHEN length(time) = 5 AND instr(time, ':') = 3 THEN time || ':00'
                ELSE time
            END
        ";

        Log::info("🕒 [Upcoming Events] Normalized Time SQL: " . $normalizedTime);

        $upcomingEvents = Event::query()
            ->whereRaw("datetime(date || ' ' || ($normalizedTime)) > datetime(?)", [$now])
            ->orderBy('date')
            ->orderByRaw("($normalizedTime)")
            ->get();

        Log::info("📅 [Upcoming Events] Query Results: ", $upcomingEvents->toArray());

        $upcomingEvents->map(function ($event) {
            $event->formatted_time = Carbon::parse($event->time)->format('h:i A');
            $event->image_url = $event->media_path ? asset('storage/' . $event->media_path) : null;
            return $event;
        });

        return response()->json($upcomingEvents);
    });
});

/*
|--------------------------------------------------------------------------
| Society Approvers (mutations)
|--------------------------------------------------------------------------
*/
Route::post('/society-approvers', [SocietyApproverController::class, 'store']);
Route::put('/society-approvers/{id}', [SocietyApproverController::class, 'update']);
Route::delete('/society-approvers/{id}', [SocietyApproverController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Utilities
|--------------------------------------------------------------------------
*/
Route::get('/check-time', function () {
    $now = now();
    Log::info("🕒 [TEST] Current Laravel Time: " . $now);
    return response()->json(['now' => $now]);
});

// Logout
Route::middleware('auth:sanctum')->post('/logout', function () {
    Auth::guard('web')->logout();
    return response()->json(['message' => 'Logged out']);
});

// Admin auth
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| NEW: Admin session probe (fixes /api/admin/me 401 in UI)
| - Requires Sanctum cookie session.
| - Returns 401 if not logged-in or not an admin.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user();
        if (!$user || ($user->user_type ?? null) !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json($user);
    });
});

/*
|--------------------------------------------------------------------------
| REMOVED (old/duplicate):
| Route::middleware('auth:api')->get('/events/upcoming', 'EventController@getUpcomingEvents');
| Because you’re using Sanctum session auth, and the old route pointed to a non-existing
| method + wrong guard.
|--------------------------------------------------------------------------
*/
