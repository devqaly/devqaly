<?php

namespace Tests\Feature\Console\Commands\Subscription;

use App\Enum\Company\CompanyBlockedReasonEnum;
use App\Models\Company\Company;
use App\Models\Project\Project;
use app\services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\UsesSubscriptionTrait;
use Tests\TestCase;

class PerformChecksOnTrialEndTest extends TestCase
{
    use RefreshDatabase, UsesSubscriptionTrait;

    public function test_company_ending_trial_with_subscription_doesnt_get_blocked(): void
    {
        $company = Company::factory()->create(['trial_ends_at' => now()->addMinutes(5)]);

        $this->createSubscriptionForCompany($company, $this->subscriptionService->getGoldMonthlyPricingId());

        $this->artisan('app:perform-checks-on-trial-end')->assertSuccessful();

        $this->assertDatabaseHas((new Company())->getTable(), [
            'id' => $company->id,
            'blocked_reasons' => null,
        ]);
    }

    public function test_company_gets_blocked_on_end_trial_when_using_more_resources_than_allowed_on_free_plan(): void
    {
        /** @var Company $company */
        $company = Company::factory()
            ->withMembers(SubscriptionService::MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY + 1)
            ->create(['trial_ends_at' => now()->addMinutes(5)]);

        Project::factory()
            ->count(SubscriptionService::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY + 1)
            ->create(['company_id' => $company->id]);

        $this->artisan('app:perform-checks-on-trial-end')->assertSuccessful();

        $company->refresh();

        $this->assertEquals(json_encode($company->blocked_reasons), json_encode([
            [
                'reason' => CompanyBlockedReasonEnum::TRIAL_FINISHED_AND_HAS_MORE_MEMBERS_THAN_ALLOWED_ON_FREE_PLAN->value,
                'description' => 'Your free trial have ended and you have more members than it is allowed on free plan',
                'possibleResolution' => 'You can either sign up to a paid plan or remove members from your company'
            ],
            [
                'reason' => CompanyBlockedReasonEnum::TRIAL_FINISHED_AND_HAS_MORE_PROJECTS_THAN_ALLOWED_ON_FREE_PLAN->value,
                'description' => 'Your free trial have ended and you have more projects than it is allowed on free plan',
                'possibleResolution' => 'You can either sign up to a paid plan or remove projects from your company'
            ]
        ]));
    }
}
