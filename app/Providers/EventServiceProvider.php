<?php

namespace App\Providers;

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
    \App\Events\UserRegistered::class => [
        \App\Listeners\SendWelcomeEmail::class,
    ],
    \App\Events\UserLoggedIn::class => [
        \App\Listeners\LogUserLogin::class,
    ],
    \App\Events\AddAgent::class => [
        \App\Listeners\SendWelcomeEmailAgent::class,
    ],
    \App\Events\BookingCreated::class => [
        \App\Listeners\BookingApply::class,
    ],
    \App\Events\BillCreated::class => [
        \App\Listeners\BillMail::class,
        \App\Listeners\SendBillToSlack::class,
    ],
    \App\Events\AppointmentCreated::class => [
        \App\Listeners\AppointmentApply::class,
    ],
    \App\Events\WorkCreated::class => [
        \App\Listeners\SendWorkToSlack::class,
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
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    /**
     * Get the directories that should be used to discover events and listeners.
     *
     * @return array<int, string>
     */
    public function discoverEventsWithin(): array
    {
        return [
            app_path('Listeners'),
        ];
    }
}
