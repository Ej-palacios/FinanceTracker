<?php

namespace App\Providers;

use App\Events\ExchangeApproved;
use App\Events\ExchangeRequestCreated;
use App\Listeners\SendApprovalNotification;
use App\Listeners\SendExchangeNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ExchangeRequestCreated::class => [
            SendExchangeNotification::class,
        ],
        ExchangeApproved::class => [
            SendApprovalNotification::class,
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
}
