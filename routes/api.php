<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;

// ✅ Public routes
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/signin', [LoginController::class, 'login']);

// ✅ Authenticated user fetch
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ Event creation (by logged-in user)
Route::middleware('auth:sanctum')->post('/events', [EventController::class, 'store']);

// ✅ Admin-only routes
Route::middleware('auth:sanctum')->group(function () {
    // 🔹 User management
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::put('/users/{id}', [UserController::class, 'update']);

    // 🔹 Event views for admin
    Route::get('/events/pending', [EventController::class, 'pending']);
    Route::get('/events/all', [EventController::class, 'all']);
    Route::get('/events/rejected', [EventController::class, 'rejected']); // ✅ NEW
});
