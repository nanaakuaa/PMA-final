<?php

namespace App\Listeners;

use App\Events\PasswordDeleted;
use App\Models\Notification;
use App\Models\User;
use App\Services\PushNotificationService;

class NotifyPasswordDeleted
{
    public function handle(PasswordDeleted $event): void
    {
        // Notify all users in the same department
        $users = User::where('department_id', $event->password->department_id)
            ->where('id', '!=', $event->deletedBy->id)
            ->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'password_id' => $event->password->id,
                'triggered_by_user_id' => $event->deletedBy->id,
                'action' => 'deleted',
                'message' => "{$event->deletedBy->name} deleted the password: {$event->password->title}",
                'is_read' => false,
            ]);
        }

        // Optional: Send web push notification (best-effort)
        try {
            app(PushNotificationService::class)->send(
                'Password Deleted',
                "{$event->deletedBy->name} deleted: {$event->password->title}",
                url('/passwords')
            );
        } catch (\Throwable $e) {
            // swallow errors; push is non-critical
        }
    }
}
