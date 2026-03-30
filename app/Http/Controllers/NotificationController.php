<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(25);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        if (! $request->user()->can('view', $notification)) {
            throw new AccessDeniedHttpException;
        }

        $notification->markAsRead();

        activity()
            ->causedBy($request->user())
            ->event('notification.read')
            ->withProperties(['notification_id' => $notification->id])
            ->log('Portal notification marked read');

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        activity()
            ->causedBy($request->user())
            ->event('notification.read_all')
            ->log('All portal notifications marked read');

        return back()->with('status', __('All notifications marked as read.'));
    }
}
