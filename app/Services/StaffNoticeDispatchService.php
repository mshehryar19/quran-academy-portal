<?php

namespace App\Services;

use App\Mail\StaffNoticeMail;
use App\Models\StaffNotice;
use App\Models\User;
use App\Notifications\PortalAlert;
use Illuminate\Support\Facades\Mail;

class StaffNoticeDispatchService
{
    public function dispatch(StaffNotice $notice): void
    {
        $notice->load(['roleTargets', 'userTargets']);
        $users = $notice->resolveRecipientUsers();

        $channels = $notice->channels ?? [];
        $channels = is_array($channels) ? $channels : [];

        $wantsPortal = count(array_intersect($channels, ['portal', 'database'])) > 0;
        $wantsEmail = in_array('email', $channels, true);
        $wantsWhatsapp = in_array('whatsapp', $channels, true);

        foreach ($users as $user) {
            $actionUrl = route('staff-notices.show', $notice);

            if ($wantsPortal) {
                $via = ['database'];
                if ($wantsWhatsapp) {
                    $via[] = 'whatsapp';
                }
                $user->notify(new PortalAlert(
                    $notice->title,
                    $notice->short_alert,
                    'staff_notice:'.$notice->category,
                    $actionUrl,
                    $via
                ));
            } elseif ($wantsWhatsapp) {
                $user->notify(new PortalAlert(
                    $notice->title,
                    $notice->short_alert,
                    'staff_notice:'.$notice->category,
                    $actionUrl,
                    ['whatsapp']
                ));
            }

            if ($wantsEmail && $user->email) {
                Mail::to($user->email)->send(new StaffNoticeMail($notice));
            }
        }

        activity()
            ->performedOn($notice)
            ->causedBy($notice->created_by_user_id ? User::find($notice->created_by_user_id) : null)
            ->event('staff_notice.dispatched')
            ->withProperties([
                'channels' => $channels,
                'recipient_count' => $users->count(),
            ])
            ->log('Staff notice dispatched');
    }
}
