<?php

namespace App\Notifications\Channels;

use App\Services\WhatsAppNotificationRecorder;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function __construct(
        private readonly WhatsAppNotificationRecorder $recorder
    ) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $payload = $notification->toWhatsApp($notifiable);
        $this->recorder->record($notifiable, $notification::class, $payload);
    }
}
