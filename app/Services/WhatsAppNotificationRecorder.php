<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class WhatsAppNotificationRecorder
{
    public function record(object $notifiable, string $notificationClass, array $payload): void
    {
        activity()
            ->performedOn($notifiable instanceof Model ? $notifiable : null)
            ->event('notification.whatsapp_prepared')
            ->withProperties([
                'notification' => $notificationClass,
                'notifiable_type' => is_object($notifiable) ? $notifiable::class : null,
                'notifiable_id' => $notifiable instanceof Model ? $notifiable->getKey() : null,
                'payload' => $payload,
                'live_send' => config('notifications.whatsapp.enabled') && ! config('notifications.whatsapp.log_only'),
            ])
            ->log('WhatsApp channel: message prepared (integration-ready; live send disabled or not configured)');
    }
}
