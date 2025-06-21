<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;

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

 
