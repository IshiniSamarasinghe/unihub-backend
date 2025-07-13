<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

// ✅ Public routes
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/signin', [LoginController::class, 'login']);

// ✅ FCM token saving and testing
Route::post('/save-token', [FirebaseNotificationController::class, 'saveToken']);
Route::post('/test-fcm', [FirebaseNotificationController::class, 'testNotification']);
Route::post('/send-notification', [FirebaseNotificationController::class, 'send']); // Optional
Route::post('/broadcast-notification', [FirebaseNotificationController::class, 'broadcast']);

// ✅ Public event routes
Route::get('/events/approved', [EventController::class, 'approved']);
Route::get('/events/all', [EventController::class, 'all']);
Route::get('/events/pending', [EventController::class, 'pending']);
Route::get('/events/rejected', [EventController::class, 'rejected']);
Route::get('/events/past-series', [EventController::class, 'pastSeries']);

// ✅ Store routes (public)
Route::get('/store-items', [StoreItemController::class, 'index']);
Route::post('/store-items', [StoreItemController::class, 'store']);
Route::delete('/store-items/{id}', [StoreItemController::class, 'destroy']);
Route::put('/store-items/{id}', [StoreItemController::class, 'update']);

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

    // 🔹 Event operations
    Route::post('/events', [EventController::class, 'store']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::put('/events/{id}', [EventController::class, 'update']);

    // 🔹 Super user's My Events
    Route::get('/events/mine', [EventController::class, 'mine']);

    // ✅ ✅ Event Media Upload (Super Users Only)
    Route::post('/event-media/upload', [EventMediaController::class, 'upload']);

    // 🔹 Event media view
    Route::get('/event-media/{eventId}', [EventMediaController::class, 'forEvent']);

    // ✅ ✅ Keep this wildcard route LAST inside this group
    Route::get('/events/{id}', [EventController::class, 'show']);
});

// ✅ Test endpoint
Route::get('/check-time', function () {
    $now = now();
    Log::info("🕒 [TEST] Current Laravel Time: " . $now);
    return response()->json(['now' => $now]);
});

// ✅ log out
Route::middleware('auth:sanctum')->post('/logout', function () {
    Auth::guard('web')->logout();
    return response()->json(['message' => 'Logged out']);
});

// ✅ Admin login
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
