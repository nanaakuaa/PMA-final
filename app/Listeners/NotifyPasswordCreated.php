<?php

namespace App\Listeners;

use App\Events\PasswordCreated;
use App\Models\Notification;
use App\Models\User;
use App\Services\PushNotificationService;
use App\Notifications\PasswordActionNotification;

class NotifyPasswordCreated
{
    public function handle(PasswordCreated $event): void
    {
        // Notify all users (except actor); respect per-user web_push_enabled
        $users = User::where('id', '!=', $event->createdBy->id)
            ->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'password_id' => $event->password->id,
                'triggered_by_user_id' => $event->createdBy->id,
                'action' => 'created',
                'message' => "{$event->createdBy->name} created a new password: {$event->password->title}",
                'is_read' => false,
            ]);
        }

        // Optional: Send web push notification (best-effort) to all enabled users
        try {
            foreach ($users as $user) {
                if ($user->web_push_enabled) {
                    $user->notify(new PasswordActionNotification('created', $event->password, $event->createdBy));
                }
            }
        } catch (\Throwable $e) {
            // swallow errors; push is non-critical
        }
    }
}
