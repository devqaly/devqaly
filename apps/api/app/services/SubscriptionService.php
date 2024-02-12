<?php

namespace app\services;

use App\Models\Company\Company;

class SubscriptionService
{
    const MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY = 10;

    const SUBSCRIPTION_DEFAULT_NAME = 'default';

    const SUBSCRIPTION_INITIAL_TRIAL_DAYS = 30;

    public function canCreateProject(Company $company): bool
    {
        return $company->projects()->count() < self::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY;
    }
}
