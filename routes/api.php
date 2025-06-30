<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocietyApproverController;

// ✅ Public routes
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/signin', [LoginController::class, 'login']);

// ✅ Authenticated user fetch
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ All Protected Routes (Admin & Logged-in Users)
Route::middleware('auth:sanctum')->group(function () {
    // 🔹 Dashboard metrics
    Route::get('/dashboard-metrics', [DashboardController::class, 'metrics']);

    // 🔹 User management
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // 🔹 Event management
    Route::post('/events', [EventController::class, 'store']);          // For event creation
    Route::get('/events/pending', [EventController::class, 'pending']); // For admin
    Route::get('/events/rejected', [EventController::class, 'rejected']);// For admin
    Route::get('/events/all', [EventController::class, 'all']);         // For admin
    Route::get('/events/approved', [EventController::class, 'approved']); // ✅ For frontend display

    // 🔹 Society Approvers management
    Route::get('/approvers', [SocietyApproverController::class, 'index']);
    Route::post('/approvers', [SocietyApproverController::class, 'store']);
    Route::put('/approvers/{id}', [SocietyApproverController::class, 'update']);
    Route::delete('/approvers/{id}', [SocietyApproverController::class, 'destroy']);
});
