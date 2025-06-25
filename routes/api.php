<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwilioController;

// Public route for registration

Route::post('/register', [RegisteredUserController::class, 'store']);

// Protected route for getting authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', [UserController::class, 'index']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::put('/users/{id}', [UserController::class, 'update']);

Route::post('/events', [EventController::class, 'store']);

Route::post('/whatsapp-reply', [UltraMsgController::class, 'handleReply']);
Route::post('/whatsapp-reply', function (Request $request) {
    Log::info('🌐 Webhook received:', $request->all());

    // Handle the message content
    $message = $request->input('data.body');

    if (str_starts_with($message, 'APPROVE-')) {
        // your approval logic
    } elseif (str_starts_with($message, 'REJECT-')) {
        // your rejection logic
    }

    return response()->json(['status' => 'OK']);
});

 