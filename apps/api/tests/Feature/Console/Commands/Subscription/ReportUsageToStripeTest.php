<?php

namespace Tests\Feature\Console\Commands\Subscription;

use App\Models\Company\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use tests\Support\UsesSubscriptionTrait;
use Tests\TestCase;

class ReportUsageToStripeTest extends TestCase
{
    use RefreshDatabase, UsesSubscriptionTrait;

    public function test_company_that_reported_usage_this_month_is_not_reported_again(): void
    {
        $twoDaysAgo = now()->subDays(2);

        $company = Company::factory()->create(['last_time_reported_usage_to_stripe' => $twoDaysAgo]);

        $this->createSubscriptionForCompany($company, $this->subscriptionService->getGoldMonthlyPricingId());

        $this->artisan('app:report-usage-to-stripe')->assertSuccessful();

        $this->assertDatabaseHas((new Company())->getTable(), [
            'id' => $company->id,
            'last_time_reported_usage_to_stripe' => $twoDaysAgo->toDateTimeString(),
        ]);
    }

    public function test_companies_without_subscription_are_not_reporting_usage(): void
    {
        $yesterday = now()->subDay();

        $company = Company::factory()->create(['last_time_reported_usage_to_stripe' => $yesterday]);

        $this->artisan('app:report-usage-to-stripe')->assertSuccessful();

        $this->assertDatabaseHas((new Company())->getTable(), [
            'id' => $company->id,
            'last_time_reported_usage_to_stripe' => $yesterday->toDateTimeString(),
        ]);
    }

    public function test_company_have_usage_reported_to_stripe(): void
    {
        $lastMonth = now()->subMonth();
        $totalMembersInvited = rand(1, 5);

        /** @var Company $company */
        $company = Company::factory()
            ->withMembers($totalMembersInvited)
            ->create(['last_time_reported_usage_to_stripe' => $lastMonth]);

        $this->createSubscriptionForCompany($company, $this->subscriptionService->getGoldMonthlyPricingId());

        $this->artisan('app:report-usage-to-stripe')->assertSuccessful();

        $company->refresh();

        $this->assertEquals(now()->month, $company->last_time_reported_usage_to_stripe->month);

        $usages = $company->subscription()->usageRecords();

        $this->assertEquals(
            $usages->first()['total_usage'],
            $totalMembersInvited + 1,
            'The `total_usage` reported to stripe should be equal to number members invited plus the company owner'
        );
    }
}
