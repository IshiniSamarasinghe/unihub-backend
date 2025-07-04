<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\NotificationToken;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\Messaging\NotFound;

class SendUpcomingEventNotifications extends Command
{
    protected $signature = 'notify:upcoming-events';
    protected $description = 'Send push notifications for events happening soon';

    public function handle()
    {
        $now = Carbon::now();
        $cutoff = $now->copy()->addHour();

        $events = Event::where('status', 'approved')
            ->whereNull('notified_at')
            ->whereRaw("datetime(date || ' ' || time) BETWEEN ? AND ?", [
                $now->format('Y-m-d H:i:s'),
                $cutoff->format('Y-m-d H:i:s')
            ])
            ->get();

        if ($events->isEmpty()) {
            $this->info('📭 No events to notify.');
            return 0;
        }

        $factory = (new Factory)->withServiceAccount(
            storage_path('app/unihub-notifications-firebase-adminsdk-fbsvc-20042521b7.json')
        );
        $messaging = $factory->createMessaging();

        $tokens = NotificationToken::whereHas('user', function ($query) {
            $query->where('notifications_enabled', true);
        })->pluck('token')->toArray();

        if (empty($tokens)) {
            $this->warn('⚠️ No eligible FCM tokens found.');
            return 0;
        }

        foreach ($events as $event) {
            $formattedTime = Carbon::parse($event->time)->format('g:i A');
            $formattedDate = Carbon::parse($event->date)->format('F j');

            $notification = Notification::create()
                ->withTitle('🎉 Upcoming Event: ' . $event->name)
                ->withBody("📅 Today at {$formattedTime}\n🏫 {$event->university}\n👉 Don’t miss it!");

            $message = CloudMessage::new()->withNotification($notification);

            $successful = 0;

            foreach ($tokens as $token) {
                try {
                    $individualMessage = $message->withChangedTarget('token', $token);
                    $messaging->send($individualMessage);

                    NotificationLog::create([
                        'event_id' => $event->id,
                        'token' => $token,
                        'status' => 'success',
                        'error_message' => null
                    ]);

                    $successful++;

                } catch (NotFound $e) {
                    Log::warning("🗑️ Invalid token deleted: {$token}");

                    NotificationLog::create([
                        'event_id' => $event->id,
                        'token' => $token,
                        'status' => 'failed',
                        'error_message' => 'Invalid token - deleted'
                    ]);

                    NotificationToken::where('token', $token)->delete();

                } catch (\Throwable $e) {
                    Log::error("❌ Failed to send to token: {$token}", [
                        'event' => $event->name,
                        'error' => $e->getMessage()
                    ]);

                    NotificationLog::create([
                        'event_id' => $event->id,
                        'token' => $token,
                        'status' => 'failed',
                        'error_message' => $e->getMessage()
                    ]);
                }
            }

            $event->update(['notified_at' => now()]);
            $this->info("✅ Notified: {$event->name} ({$successful} sent)");
        }

        return 0;
    }
}
