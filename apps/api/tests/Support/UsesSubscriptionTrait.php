<?php

namespace tests\Support;

use App\Models\Company\Company;
use App\services\SubscriptionService;

trait UsesSubscriptionTrait
{
    public SubscriptionService $subscriptionService;

    /**
     * @before
     */
    public function setupProperties()
    {
        $this->afterApplicationCreated(function () {
            $this->subscriptionService = app()->make(SubscriptionService::class);
        });
    }

    private function createSubscriptionForCompany(Company $company, string  $pricing): void
    {
        $company
            ->newSubscription('default', $pricing)
            // Quantity is necessary to set to null on metered plans
            // @see https://stackoverflow.com/a/64613077/4581336
            ->quantity(null)
            ->trialDays(SubscriptionService::SUBSCRIPTION_INITIAL_TRIAL_DAYS)
            ->create(customerOptions: [
                'metadata' => [
                    'environment' => \config('app.env')
                ]
            ]);
    }
}
