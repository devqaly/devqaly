<?php

namespace Tests\Feature\Listeners\Stripe;

use App\Enum\Company\CompanyBlockedReasonEnum;
use App\Listeners\Stripe\BlockCompanyOnSubscriptionIssues;
use App\Models\Company\Company;
use App\Models\Project\Project;
use App\services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Events\WebhookReceived;
use tests\Support\UsesSubscriptionTrait;
use Tests\TestCase;

class BlockCompanyOnSubscriptionIssuesTest extends TestCase
{
    use UsesSubscriptionTrait, RefreshDatabase;

    public function test_handle_doesnt_run_company_has_subscription_status_active_or_trialing(): void
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

        $listener = new BlockCompanyOnSubscriptionIssues(new SubscriptionService());

        $listener->handle($webhookReceived);

        $company->refresh();

        $this->assertNull($company->blocked_reasons);
    }

    public function test_company_gets_blocked_if_it_has_more_members_than_allowed_on_free_plan(): void
    {
        /** @var Company $company */
        $company = Company::factory()
            ->withMembers(SubscriptionService::MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY + 1)
            ->create();

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

        $listener = new BlockCompanyOnSubscriptionIssues(new SubscriptionService());

        $listener->handle($webhookReceived);

        $company->refresh();

        $this->assertEquals($company->blocked_reasons, [
            [
                'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
                'description' => 'Your subscription was not paid and you have more members than it is allowed on free plan',
                'possibleResolution' => 'You can add a new payment method in subscriptions page',
                'subscriptionStatus' => $webhookReceived->payload['data']['object']['status'],
            ]
        ]);
    }

    public function test_company_gets_blocked_if_it_has_more_projects_than_allowed_on_free_plan(): void
    {
        /** @var Company $company */
        $company = Company::factory()->create();

        Project::factory()
            ->count(SubscriptionService::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY + 1)
            ->create(['company_id' => $company->id]);

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

        $listener = new BlockCompanyOnSubscriptionIssues(new SubscriptionService());

        $listener->handle($webhookReceived);

        $company->refresh();

        $this->assertEquals($company->blocked_reasons, [
            [
                'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
                'description' => 'Your subscription was not paid and you have more projects than it is allowed on free plan',
                'possibleResolution' => 'You can add a new payment method in subscriptions page',
                'subscriptionStatus' => $webhookReceived->payload['data']['object']['status'],
            ]
        ]);
    }

    public function test_company_gets_blocked_if_it_has_more_projects_and_members_than_allowed_on_free_plan(): void
    {
        /** @var Company $company */
        $company = Company::factory()
            ->withMembers(SubscriptionService::MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY + 1)
            ->create();

        Project::factory()
            ->count(SubscriptionService::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY + 1)
            ->create(['company_id' => $company->id]);

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

        $listener = new BlockCompanyOnSubscriptionIssues(new SubscriptionService());

        $listener->handle($webhookReceived);

        $company->refresh();

        $this->assertEqualsCanonicalizing($company->blocked_reasons, [
            [
                'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
                'description' => 'Your subscription was not paid and you have more members than it is allowed on free plan',
                'possibleResolution' => 'You can add a new payment method in subscriptions page',
                'subscriptionStatus' => $webhookReceived->payload['data']['object']['status'],
            ],
            [
                'reason' => CompanyBlockedReasonEnum::SUBSCRIPTION_WAS_NOT_PAID->value,
                'description' => 'Your subscription was not paid and you have more projects than it is allowed on free plan',
                'possibleResolution' => 'You can add a new payment method in subscriptions page',
                'subscriptionStatus' => $webhookReceived->payload['data']['object']['status'],
            ]
        ]);
    }
}
