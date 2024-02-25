<?php

namespace tests\Support;

use App\Models\Company\Company;
use App\services\SubscriptionService;
use Laravel\Cashier\Subscription;

trait UsesSubscriptionTrait
{
    public SubscriptionService $subscriptionService;

    public Subscription $subscription;

    /**
     * @before
     */
    public function setupProperties()
    {
        $this->afterApplicationCreated(function () {
            $this->subscriptionService = app()->make(SubscriptionService::class);
        });
    }

    private function createSubscriptionForCompany(Company $company, string $pricing): void
    {
        $this->subscription = $company
            ->newSubscription('default')
            ->meteredPrice($pricing)
            ->create(customerOptions: ['metadata' => ['environment' => \config('app.env')]]);
    }
}
