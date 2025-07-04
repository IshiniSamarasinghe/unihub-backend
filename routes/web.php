<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Controllers\FCMController;



/*
|--------------------------------------------------------------------------
| API + Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('/signin', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return response()->json(['message' => 'Login successful']);
    }

    return response()->json(['message' => 'Invalid credentials'], 422);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['message' => 'Logged out']);
});

/*
|--------------------------------------------------------------------------
| Event Approval Routes (for email links)
|--------------------------------------------------------------------------
*/

Route::get('/approve', function (Request $request) {
    $event = Event::where('approval_token', $request->token)->first();

    if (!$event) {
        return response()->view('approval-response', [
            'message' => '❌ Invalid or expired approval token.',
            'status' => 'error'
        ]);
    }

    $event->status = 'approved';
    $event->save();

    return response()->view('approval-response', [
        'message' => '✅ Event approved successfully!',
        'status' => 'success'
    ]);
});

Route::get('/reject', function (Request $request) {
    $event = Event::where('approval_token', $request->token)->first();

    if (!$event) {
        return response()->view('approval-response', [
            'message' => '❌ Invalid or expired rejection token.',
            'status' => 'error'
        ]);
    }

    $event->status = 'rejected';
    $event->save();

    return response()->view('approval-response', [
        'message' => '❌ Event was rejected successfully.',
        'status' => 'rejected'
    ]);
});

/*
|--------------------------------------------------------------------------
| Dev Helper Route (phpinfo)
|--------------------------------------------------------------------------
*/

Route::get('/phpinfo', function () {
    phpinfo();
});

/*
|--------------------------------------------------------------------------
| Catch-All: React Frontend
|--------------------------------------------------------------------------
*/

Route::view('/{any}', 'react')->where('any', '.*');

Route::get('/test-push', function () {
    $token = 'YOUR_DEVICE_FCM_TOKEN'; // replace this with the token printed in your browser console
    return app(FCMController::class)->sendNotification($token);
});

 
