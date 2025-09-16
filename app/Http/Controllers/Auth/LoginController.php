<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Handles SPA redirect for GET /login so Laravel doesn't error.
     * Keeps your routes/web.php intact.
     */
    public function showLoginForm()
    {
        // If your React route is different, change '/signin' accordingly.
        return redirect('/signin');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            try {
                // Fetch upcoming events (with pagination or optimized selection)
                $upcomingEvents = Event::where('event_date', '>', Carbon::now())
                    ->select('name', 'event_date', 'image_path')
                    ->paginate(5);  // Adjust the pagination as necessary

                return response()->json([
                    'message' => '✅ Login successful.',
                    'upcoming_events' => $upcomingEvents,
                ]);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error fetching events.'], 500);
            }
        }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
