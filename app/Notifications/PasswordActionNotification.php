<?php

namespace App\Notifications;

use App\Models\Password;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class PasswordActionNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $action, // created|updated|deleted
        protected Password $password,
        protected User $actor
    ) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class, 'database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'action' => $this->action,
            'password_id' => $this->password->id,
            'title' => $this->password->title,
            'actor' => $this->actor->only(['id','name']),
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $verb = match($this->action){
            'created' => 'created',
            'updated' => 'updated',
            'deleted' => 'deleted',
            default => $this->action,
        };

        return (new WebPushMessage)
            ->title('Password ' . ucfirst($verb))
            ->body($this->actor->name . ' ' . $verb . ': ' . $this->password->title)
            ->icon('/icon.png')
            ->data([
                'password_id' => (string) $this->password->id,
                'url' => url('/passwords'),
            ]);
    }
}
