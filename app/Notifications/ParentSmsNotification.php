<?php

namespace App\Notifications;

use App\Services\SmsNotificationService;
use Illuminate\Notifications\Notification;

class ParentSmsNotification extends Notification
{

    public function __construct(
        public string $type,
        public string $message,
        public string $title
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
