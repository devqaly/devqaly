<?php

namespace App\Listeners\Stripe;

use App\Enum\Company\CompanyBlockedReasonEnum;
use App\Models\Company\Company;
use App\services\SubscriptionService;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class BlockCompanyOnSubscriptionIssues
{
    private SubscriptionService $subscriptionService;

    protected array $reasons = [];

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'customer.subscription.updated') return;

        if ($this->subscriptionIsActiveOrTrialing($event)) return;

        /** @var Company $company */
        $company = $this->subscriptionService->getCompanyFromStripeId(
            $event->payload['data']['object']['id']
        );

        if ($this->subscriptionService->hasMoreMembersThanAllowedOnFreePlan($company)) {
            $this->addHasMoreMembersToReasonsToBeBlocked($event);
        }

        if ($this->subscriptionService->hasMoreProjectsThanAllowedOnFreePlan($company)) {
            $this->addHasMoreProjectsToReasonsToBeBlocked($event);
        }

        $this->markCompanyAsBlocked($company);
    }

    private function addHasMoreMembersToReasonsToBeBlocked(WebhookReceived $event): void
    {
        $this->reasons[] = [
            'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
            'description' => 'Your subscription was not paid and you have more members than it is allowed on free plan',
            'possibleResolution' => 'You can add a new payment method in subscriptions page',
            'subscriptionStatus' => $event->payload['data']['object']['status'],
        ];
    }

    private function addHasMoreProjectsToReasonsToBeBlocked(WebhookReceived $event): void
    {
        $this->reasons[] = [
            'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
            'description' => 'Your subscription was not paid and you have more projects than it is allowed on free plan',
            'possibleResolution' => 'You can add a new payment method in subscriptions page',
            'subscriptionStatus' => $event->payload['data']['object']['status'],
        ];
    }

    private function subscriptionIsActiveOrTrialing(WebhookReceived $event): bool
    {
        $subscriptionStatus = $event->payload['data']['object']['status'];

        return in_array($subscriptionStatus, ['trialing', 'active']);
    }

    private function markCompanyAsBlocked(Company $company): void
    {
        if (empty($this->reasons)) return;

        Log::info(sprintf('Blocking company %s with subscription status %s', $company->id, $company->subscription()->stripe_status));

        $company->blocked_reasons = $this->reasons;
        $company->save();
    }
}
