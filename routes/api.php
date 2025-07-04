<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocietyApproverController;
use App\Http\Controllers\FirebaseNotificationController;
use App\Http\Controllers\UserNotificationPreferenceController;

// ✅ Public routes
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/signin', [LoginController::class, 'login']);

// ✅ FCM token saving and testing
Route::post('/save-token', [FirebaseNotificationController::class, 'saveToken']);
Route::post('/test-fcm', [FirebaseNotificationController::class, 'testNotification']);
Route::post('/send-notification', [FirebaseNotificationController::class, 'send']); // Optional
Route::post('/broadcast-notification', [FirebaseNotificationController::class, 'broadcast']);

// ✅ Event routes (ordering matters!)
Route::get('/events/approved', [EventController::class, 'approved']);
Route::get('/events/all', [EventController::class, 'all']);
Route::get('/events/pending', [EventController::class, 'pending']);
Route::get('/events/rejected', [EventController::class, 'rejected']);
Route::get('/events/past-series', [EventController::class, 'pastSeries']);

// ✅ Fetch single event by ID (keep this last)
Route::get('/events/{id}', [EventController::class, 'show']);

// ✅ Authenticated user fetch
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ Protected routes (only for logged-in users)
Route::middleware('auth:sanctum')->group(function () {
    // 🔹 Dashboard metrics
    Route::get('/dashboard-metrics', [DashboardController::class, 'metrics']);

    // 🔹 User management
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // 🔹 Event creation
    Route::post('/events', [EventController::class, 'store']);

    // 🔹 Society approvers
    Route::get('/approvers', [SocietyApproverController::class, 'index']);
    Route::post('/approvers', [SocietyApproverController::class, 'store']);
    Route::put('/approvers/{id}', [SocietyApproverController::class, 'update']);
    Route::delete('/approvers/{id}', [SocietyApproverController::class, 'destroy']);

    // 🔹 Notification preferences
    Route::put('/user/notifications', [UserNotificationPreferenceController::class, 'update']);
});

// ✅ Server time checker for testing cronjob
Route::get('/check-time', function () {
    $now = now();
    Log::info("🕒 [TEST] Current Laravel Time: " . $now);
    return response()->json(['now' => $now]);
});
