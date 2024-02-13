<?php

namespace app\services;

use App\Models\Company\Company;

class SubscriptionService
{
    const MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY = 1;

    const MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY = 3;

    const SUBSCRIPTION_GOLD_NAME = 'gold';

    const SUBSCRIPTION_ENTERPRISE_NAME = 'enterprise';

    const SUBSCRIPTION_INITIAL_TRIAL_DAYS = 30;

    public function canCreateProject(Company $company): bool
    {
        if ($company->subscribed(self::SUBSCRIPTION_ENTERPRISE_NAME)) {
            return true;
        }

        if ($company->subscribed(self::SUBSCRIPTION_GOLD_NAME)) {
            return $company->projects()->count() < self::MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY;
        }

        return $company->projects()->count() < self::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY;
    }
}
