<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationToken;

class NotificationTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        NotificationToken::updateOrCreate(
            ['token' => $request->token],
            ['token' => $request->token]
        );

        return response()->json(['message' => '✅ Token stored successfully']);
    }
}
