<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TwilioController;

// ✅ Public routes
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/signin', [LoginController::class, 'login']);

// ✅ Protected route to fetch authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// routes/api.php
Route::middleware('auth:sanctum')->post('/events', [EventController::class, 'store']);


// ✅ Protected routes for admin operations
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::put('/users/{id}', [UserController::class, 'update']);
     
});

// ✅ WhatsApp reply routes
Route::post('/whatsapp-reply', [TwilioController::class, 'handleReply']);
Route::post('/whatsapp-reply', function (Request $request) {
    Log::info('🌐 Webhook received:', $request->all());

    $message = $request->input('data.body');
    if (str_starts_with($message, 'APPROVE-')) {
        // Approval logic here
    } elseif (str_starts_with($message, 'REJECT-')) {
        // Rejection logic here
    }

    return response()->json(['status' => 'OK']);
});
