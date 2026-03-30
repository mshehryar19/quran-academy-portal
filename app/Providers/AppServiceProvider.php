<?php

namespace App\Providers;

use App\Notifications\Channels\WhatsAppChannel;
use App\Policies\DatabaseNotificationPolicy;
use App\Services\SettingsService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Notification::extend('whatsapp', fn ($app) => $app->make(WhatsAppChannel::class));

        Gate::policy(DatabaseNotification::class, DatabaseNotificationPolicy::class);

        View::composer(['layouts.header', 'layouts.app', 'layouts.auth'], function ($view): void {
            try {
                $portalName = app(SettingsService::class)->get('system_name', config('app.name'));
            } catch (\Throwable) {
                $portalName = config('app.name');
            }
            $view->with('portalDisplayName', $portalName);
        });

        View::composer('layouts.header', function ($view): void {
            $user = auth()->user();
            if (! $user) {
                return;
            }

            $view->with([
                'unreadNotificationsCount' => $user->unreadNotifications()->count(),
                'recentNotifications' => $user->notifications()->latest()->limit(8)->get(),
            ]);
        });

        Event::listen(Login::class, function (Login $event): void {
            activity()
                ->causedBy($event->user)
                ->event('auth.login')
                ->log('User logged in');
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if (! $event->user) {
                return;
            }

            activity()
                ->causedBy($event->user)
                ->event('auth.logout')
                ->log('User logged out');
        });

        Event::listen(Failed::class, function (Failed $event): void {
            activity()
                ->withProperties([
                    'email' => $event->credentials['email'] ?? null,
                ])
                ->event('auth.failed')
                ->log('Failed login attempt');
        });
    }
}
