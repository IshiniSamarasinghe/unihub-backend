<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Http\Controllers\FCMController;
use App\Http\Controllers\Auth\LoginController;
// ✅ add this import
use App\Http\Controllers\Admin\SocietyAdminController;

/*
|--------------------------------------------------------------------------
| API + Auth Routes (session-based)
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
| Same-origin Admin Endpoints (use session "web" guard)
| These are what your React admin pages should call.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth' /*, 'is_admin'  // add your admin middleware if you have one */])->group(function () {
    // Who am I? (used by AdminLayout)
    Route::get('/admin/me', function () {
        $u = auth()->user();
        return response()->json([
            'id'          => $u->id,
            'name'        => $u->name,
            'email'       => $u->email,
            'avatar_path' => $u->avatar_path ?? null,
            'avatar_url'  => $u->avatar_path ? asset('storage/' . $u->avatar_path) : null,
        ]);
    });

    // Event feeds for admin pages
    Route::get('/admin/all-events', function () {
        return Event::latest()->get();
    });

    Route::get('/admin/pending-events', function () {
        return Event::where('status', 'pending')->latest()->get();
    });

    Route::get('/admin/rejected-events', function () {
        return Event::where('status', 'rejected')->latest()->get();
    });

    // Dashboard metrics
    Route::get('/dashboard-metrics', function () {
        return response()->json([
            'totalEvents'     => Event::count(),
            'pendingEvents'   => Event::where('status', 'pending')->count(),
            'registeredUsers' => User::count(),
        ]);
    });
});

// ✅ place this BEFORE the catch-all
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Admin page to view Societies (web page, not API)
 
});

/*
|--------------------------------------------------------------------------
| Admin "me" endpoint (API guard) — kept for compatibility if used elsewhere
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->get('/api/admin/me', function () {
    $admin = auth('admin')->user();

    return response()->json([
        'admin' => [
            'id'          => $admin->id,
            'name'        => $admin->name,
            'email'       => $admin->email,
            'avatar_path' => $admin->avatar_path,
            'avatar_url'  => $admin->avatar_path ? asset('storage/' . $admin->avatar_path) : null,
        ],
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
| Test Push Notification
|--------------------------------------------------------------------------
*/
Route::get('/test-push', function () {
    $token = 'YOUR_DEVICE_FCM_TOKEN'; // replace with a real token printed in your browser console
    return app(FCMController::class)->sendNotification($token);
});

/*
|--------------------------------------------------------------------------
| Define the Login Route (required by Laravel's Authenticate Middleware)
|--------------------------------------------------------------------------
*/
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login'); // Login page
Route::post('login', [LoginController::class, 'login']); // Login submission

/*
|--------------------------------------------------------------------------
| Catch-All: React Frontend (keep this LAST)
|--------------------------------------------------------------------------
*/
Route::view('/{any}', 'react')->where('any', '.*');
