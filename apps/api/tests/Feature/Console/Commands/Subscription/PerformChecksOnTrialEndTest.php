<?php

namespace Tests\Feature\Console\Commands\Subscription;

use App\Models\Company\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Support\UsesSubscriptionTrait;
use Tests\TestCase;

class PerformChecksOnTrialEndTest extends TestCase
{
    use RefreshDatabase, UsesSubscriptionTrait;

    public function test_company_ending_trial_without_subscription_gets_blocked(): void
    {
        $company = Company::factory()->create(['trial_ends_at' => now()->addMinutes(5)]);

        $this->artisan('app:perform-checks-on-trial-end')->assertSuccessful();

        $this->assertDatabaseHas((new Company())->getTable(), [
            'id' => $company->id,
            'is_blocked_after_trial' => true,
        ]);
    }

    public function test_company_ending_trial_with_subscription_doesnt_get_blocked(): void
    {
        $company = Company::factory()->create(['trial_ends_at' => now()->addMinutes(5)]);

        $this->createSubscriptionForCompany($company, $this->subscriptionService->getGoldMonthlyPricingId());

        $this->artisan('app:perform-checks-on-trial-end')->assertSuccessful();

        $this->assertDatabaseHas((new Company())->getTable(), [
            'id' => $company->id,
            'is_blocked_after_trial' => false,
        ]);
    }
}
