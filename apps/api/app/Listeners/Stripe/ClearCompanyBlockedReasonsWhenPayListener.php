<?php

namespace App\Listeners\Stripe;

use App\Models\Company\Company;
use app\services\SubscriptionService;
use Illuminate\Support\Collection;
use Laravel\Cashier\Events\WebhookReceived;

class ClearCompanyBlockedReasonsWhenPayListener
{
    private SubscriptionService $subscriptionService;

    protected Collection $reasons;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'customer.subscription.updated') return;

        if (!$this->subscriptionIsActiveOrTrialing($event)) return;

        /** @var Company $company */
        $company = $this->subscriptionService->getCompanyFromStripeId(
            $event->payload['data']['object']['id']
        );

        $this->removeReasons($company);
    }

    public function subscriptionIsActiveOrTrialing(WebhookReceived $event): bool
    {
        $stripeStatus = $event->payload['data']['object']['status'];

        return in_array($stripeStatus, ['trialing', 'active']);
    }

    public function removeReasons(Company $company): void
    {
        $company->blocked_reasons = null;

        $company->save();
    }
}
