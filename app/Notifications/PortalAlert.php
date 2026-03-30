<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PortalAlert extends Notification
{
    /**
     * @param  list<string>  $channels
     */
    public function __construct(
        public string $title,
        public string $body,
        public string $category = 'system',
        public ?string $actionUrl = null,
        public array $channels = ['database'],
    ) {}

    /**
     * @return list<int|string>
     */
    public function via(object $notifiable): array
    {
        $via = [];
        foreach ($this->channels as $ch) {
            $ch = strtolower((string) $ch);
            if ($ch === 'portal' || $ch === 'database') {
                $via[] = 'database';
            } elseif ($ch === 'email' || $ch === 'mail') {
                $via[] = 'mail';
            } elseif ($ch === 'whatsapp') {
                $via[] = 'whatsapp';
            }
        }
        $via = array_values(array_unique($via));

        if ($via === []) {
            return ['database'];
        }

        return $via;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'category' => $this->category,
            'action_url' => $this->actionUrl,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->body)
            ->when($this->actionUrl, fn (MailMessage $m) => $m->action(__('Open in portal'), url($this->actionUrl)));
    }

    public function toWhatsApp(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'action_url' => $this->actionUrl,
        ];
    }
}
