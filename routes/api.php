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

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/signin', [LoginController::class, 'login']);

// FCM
Route::post('/save-token', [FirebaseNotificationController::class, 'saveToken']);
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
|
| Public GET for easy table loading.
| Mutations guarded by Sanctum (and you can add an 'admin' middleware later).
|
*/

// Read (public)
Route::get('/society-approvers', [SocietyApproverController::class, 'index']);

// Backward compatibility: allow frontend calling /approvers
Route::get('/approvers', [SocietyApproverController::class, 'index']);

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
    Route::get('/events/{id}', [EventController::class, 'show']); // keep last in this group

    // Society Approvers (mutations)
    Route::post('/society-approvers', [SocietyApproverController::class, 'store']);
    Route::put('/society-approvers/{id}', [SocietyApproverController::class, 'update']);
    Route::delete('/society-approvers/{id}', [SocietyApproverController::class, 'destroy']);
});

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
