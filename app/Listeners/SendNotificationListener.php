<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationCreated $event): void
    {
        $notification = $event->notification;

        // Log the notification
        \Log::info('Notification created', [
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id,
            'type' => $notification->type,
            'method' => $notification->method,
        ]);

        // Send email if enabled
        if ($notification->method === \App\Models\Notification::METHOD_EMAIL) {
            try {
                // Send email notification
                \Mail::send('emails.notification', [
                    'notification' => $notification,
                    'user' => $notification->user,
                ], function ($mail) use ($notification) {
                    $mail->to($notification->user->email)
                        ->subject($notification->title);
                });

                $notification->update([
                    'status' => \App\Models\Notification::STATUS_SENT,
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send email notification', [
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage(),
                ]);

                $notification->update([
                    'status' => \App\Models\Notification::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        // Send SMS if enabled
        if ($notification->method === \App\Models\Notification::METHOD_SMS) {
            try {
                // Send SMS notification
                // TODO: Implement SMS sending logic (e.g., using Twilio, Nexmo, etc.)
                $notification->update([
                    'status' => \App\Models\Notification::STATUS_SENT,
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send SMS notification', [
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage(),
                ]);

                $notification->update([
                    'status' => \App\Models\Notification::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        // In-app notifications are marked as sent immediately
        if ($notification->method === \App\Models\Notification::METHOD_IN_APP) {
            $notification->update([
                'status' => \App\Models\Notification::STATUS_SENT,
                'sent_at' => now(),
            ]);

            // Broadcast the notification
            broadcast($event);
        }
    }
}
