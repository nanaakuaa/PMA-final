<?php

namespace App\Listeners;

use App\Events\PasswordUpdated;
use App\Models\Notification;
use App\Models\User;
use App\Services\PushNotificationService;

class NotifyPasswordUpdated
{
    public function handle(PasswordUpdated $event): void
    {
        // Notify all users in the same department
        $users = User::where('department_id', $event->password->department_id)
            ->where('id', '!=', $event->updatedBy->id)
            ->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'password_id' => $event->password->id,
                'triggered_by_user_id' => $event->updatedBy->id,
                'action' => 'updated',
                'message' => "{$event->updatedBy->name} updated the password: {$event->password->title}",
                'is_read' => false,
            ]);
        }

        // Optional: Send web push notification (best-effort)
        try {
            app(PushNotificationService::class)->send(
                'Password Updated',
                "{$event->updatedBy->name} updated: {$event->password->title}",
                url('/passwords')
            );
        } catch (\Throwable $e) {
            // swallow errors; push is non-critical
        }
    }
}
