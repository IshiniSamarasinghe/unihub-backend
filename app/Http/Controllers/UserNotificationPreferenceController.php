<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserNotificationPreferenceController extends Controller
{
    public function update(Request $request)
    {
        $request->validate(['enabled' => 'required|boolean']);

        $user = $request->user();
        $user->notifications_enabled = $request->enabled;
        $user->save();

        return response()->json([
    'success' => true,
    'enabled' => $user->notifications_enabled
]);

    }
}

