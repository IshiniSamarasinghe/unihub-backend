<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UltraMsgWebhookController;
use App\Models\Event;

/*
|--------------------------------------------------------------------------
| API + Auth Routes
|--------------------------------------------------------------------------
*/

// ✅ Handle login request (from React)
Route::post('/signin', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return response()->json(['message' => 'Login successful']);
    }

    return response()->json(['message' => 'Invalid credentials'], 422);
});

// ✅ Logout endpoint
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['message' => 'Logged out']);
});


/*
|--------------------------------------------------------------------------
| Catch-all Frontend Routes – serve React app from Blade
|--------------------------------------------------------------------------
*/

Route::view('/{any}', 'react')->where('any', '.*');

// ✅ Approval Route
Route::get('/approve', function (Request $request) {
    $event = \App\Models\Event::where('approval_token', $request->token)->firstOrFail();
    $event->status = 'approved';
    $event->save();

    return Redirect::to('/events');
});

// ❌ Rejection Route
Route::get('/reject', function (Request $request) {
    $event = \App\Models\Event::where('approval_token', $request->token)->firstOrFail();
    $event->status = 'rejected';
    $event->save();

    return Redirect::to('/events');
});