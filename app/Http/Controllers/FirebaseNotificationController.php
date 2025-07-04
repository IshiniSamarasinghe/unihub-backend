<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationToken;
use Kreait\Firebase\Factory;

class FirebaseNotificationController extends Controller
{
    // ✅ Save token to database
    public function saveToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        DB::table('notification_tokens')->updateOrInsert(
            ['token' => $request->token],
            ['updated_at' => now(), 'created_at' => now()]
        );

        return response()->json(['message' => '✅ Token saved successfully']);
    }

    // ✅ Send test notification to one device
    public function testNotification(Request $request)
    {
        try {
            $token = $request->input('token');

            $factory = (new Factory)->withServiceAccount(
                storage_path('app/unihub-notifications-firebase-adminsdk-fbsvc-20042521b7.json')
            );
            $messaging = $factory->createMessaging();

            $message = [
                'token' => $token,
                'notification' => [
                    'title' => '🎓 UniHub Test',
                    'body' => '📢 This is a test push notification!',
                ],
            ];

            $messaging->send($message);

            return response()->json(['message' => '✅ Notification sent']);
        } catch (\Throwable $e) {
            Log::error('🔥 FCM Send Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'error' => '❌ Failed to send notification',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ Broadcast to all tokens
    public function broadcast(Request $request)
    {
        try {
            $factory = (new Factory)->withServiceAccount(
                storage_path('app/unihub-notifications-firebase-adminsdk-fbsvc-20042521b7.json')
            );
            $messaging = $factory->createMessaging();

            $tokens = NotificationToken::pluck('token')->toArray();

            if (empty($tokens)) {
                return response()->json(['error' => 'No device tokens found'], 404);
            }

            $message = [
                'notification' => [
                    'title' => $request->input('title', '🎉 UniHub Update!'),
                    'body' => $request->input('body', '🚀 Check out the latest events now!'),
                ],
                'tokens' => $tokens,
            ];

            $messaging->sendMulticast($message);

            return response()->json(['message' => '✅ Broadcast sent to all devices']);
        } catch (\Throwable $e) {
            Log::error('🔥 Broadcast Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
