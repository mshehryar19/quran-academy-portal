<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
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
