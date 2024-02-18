<?php

namespace App\Providers;

use App\Events\MixpanelEventCreated;
use App\Listeners\DispatchMixpanelListener;
use App\Listeners\Stripe\ClearCompanyBlockedReasonsWhenPayListener;
use App\Listeners\Stripe\BlockCompanyOnSubscriptionIssues;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookReceived;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MixpanelEventCreated::class => [
            DispatchMixpanelListener::class
        ],
        WebhookReceived::class => [
            BlockCompanyOnSubscriptionIssues::class,
            ClearCompanyBlockedReasonsWhenPayListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
