<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationTokenController extends Controller
{
    /**
     * Store or update an FCM token safely (idempotent, resilient).
     */
 public function store(Request $request)
{
    $data = $request->validate([
        'token' => 'required|string',
    ]);

    try {
        \App\Models\NotificationToken::updateOrCreate(
            ['token' => $data['token']],
            ['user_id' => optional($request->user())->id]
        );
        return response()->json(['ok' => true]);
    } catch (\Throwable $e) {
        \Log::error('❌ Failed to save FCM token', ['error' => $e->getMessage()]);
        return response()->json(['ok' => false, 'error' => 'save_failed'], 500);
    }
}

}
