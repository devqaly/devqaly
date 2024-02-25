<?php

namespace Tests\Feature\Listeners\Stripe;

use App\Enum\Company\CompanyBlockedReasonEnum;
use App\Listeners\Stripe\ClearCompanyBlockedReasonsWhenPayListener;
use App\Models\Company\Company;
use app\services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Events\WebhookReceived;
use Mockery\MockInterface;
use tests\Support\UsesSubscriptionTrait;
use Tests\TestCase;

class ClearCompanyBlockedReasonsWhenPayListenerTest extends TestCase
{
    use RefreshDatabase, UsesSubscriptionTrait;

    public function test_listener_doesnt_run_if_event_type_is_wrong(): void
    {
        $webhookReceived = new WebhookReceived([
            'type' => 'customer.subscription.created'
        ]);

        /** @var ClearCompanyBlockedReasonsWhenPayListener $listener */
        $listener = $this
            ->partialMock(
                ClearCompanyBlockedReasonsWhenPayListener::class,
                function (MockInterface $mock) {
                    $mock->shouldNotReceive('removeReasons');
                });

        $listener->handle($webhookReceived);
    }

    public function test_listener_doesnt_run_if_subscription_status_is_not_active_or_trailing(): void
    {
        $company = Company::factory()->create();

        $this->createSubscriptionForCompany($company, $this->subscriptionService->getGoldMonthlyPricingId());

        $webhookReceived = new WebhookReceived([
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => $this->subscription->stripe_id,
                    'status' => 'past_due'
                ]
            ]
        ]);

        /** @var ClearCompanyBlockedReasonsWhenPayListener $listener */
        $listener = $this
            ->partialMock(
                ClearCompanyBlockedReasonsWhenPayListener::class,
                function (MockInterface $mock) {
                    $mock->shouldNotReceive('removeReasons');
                });

        $listener->handle($webhookReceived);
    }

    public function test_company_get_reasons_removed_when_on_active_subscription(): void
    {
        $company = Company::factory()->create([
            'blocked_reasons' => [
                [
                    'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
                    'description' => 'Your subscription was not paid and you have more members than it is allowed on free plan',
                    'possibleResolution' => 'You can add a new payment method in subscriptions page',
                    'subscriptionStatus' => 'past_due',
                ],
                [
                    'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
                    'description' => 'Your subscription was not paid and you have more projects than it is allowed on free plan',
                    'possibleResolution' => 'You can add a new payment method in subscriptions page',
                    'subscriptionStatus' => 'past_due',
                ]
            ]
        ]);

        $this->createSubscriptionForCompany($company, $this->subscriptionService->getGoldMonthlyPricingId());

        $webhookReceived = new WebhookReceived([
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => $this->subscription->stripe_id,
                    'status' => 'active'
                ]
            ]
        ]);

        $listener = new ClearCompanyBlockedReasonsWhenPayListener(new SubscriptionService());

        $listener->handle($webhookReceived);

        $company->refresh();

        $this->assertNull($company->blocked_reasons);
    }

    public function test_company_get_reasons_removed_when_on_trialing_subscription(): void
    {
        $company = Company::factory()->create([
            'blocked_reasons' => [
                [
                    'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
                    'description' => 'Your subscription was not paid and you have more members than it is allowed on free plan',
                    'possibleResolution' => 'You can add a new payment method in subscriptions page',
                    'subscriptionStatus' => 'past_due',
                ],
                [
                    'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
                    'description' => 'Your subscription was not paid and you have more projects than it is allowed on free plan',
                    'possibleResolution' => 'You can add a new payment method in subscriptions page',
                    'subscriptionStatus' => 'past_due',
                ]
            ]
        ]);

        $this->createSubscriptionForCompany($company, $this->subscriptionService->getGoldMonthlyPricingId());

        $webhookReceived = new WebhookReceived([
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => $this->subscription->stripe_id,
                    'status' => 'trialing'
                ]
            ]
        ]);

        $listener = new ClearCompanyBlockedReasonsWhenPayListener(new SubscriptionService());

        $listener->handle($webhookReceived);

        $company->refresh();

        $this->assertNull($company->blocked_reasons);
    }
}
