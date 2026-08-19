<?php

namespace App\Providers;

use App\Events\PostEvent;
use App\Listeners\LogAuditoriaAuth;
use App\Listeners\PostListener;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PostEvent::class => [ PostListener::class,],
        Login::class => [
            [LogAuditoriaAuth::class, 'handleLogin'],
        ],
        Logout::class => [
            [LogAuditoriaAuth::class, 'handleLogout'],
        ],
        Failed::class => [
            [LogAuditoriaAuth::class, 'handleFailed'],
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverclases(): bool
    {
        return false;
    }
}
