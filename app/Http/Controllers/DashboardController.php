<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function metrics()
    {
         return response()->json([
            'totalEvents' => Event::count(),
            'pendingEvents' => Event::where('status', 'pending')->count(),
            'registeredUsers' => User::count(),
            'recentEvents' => Event::latest()->take(5)->get(['name', 'university', 'date', 'status']),
        ]);
    }
}
