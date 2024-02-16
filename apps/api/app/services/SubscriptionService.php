<?php

namespace app\services;

use App\Models\Company\Company;

class SubscriptionService
{
    const MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY = 1;

    const MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY = 3;

    const MAXIMUM_NUMBER_SESSIONS_FREE_PLAN_PER_COMPANY = 5;

    const MAXIMUM_NUMBER_SESSIONS_GOLD_PLAN_PER_COMPANY = 100;

    const MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY = 5;

    const SUBSCRIPTION_INITIAL_TRIAL_DAYS = 30;

    public function canCreateProject(Company $company): bool
    {
        if ($company->subscribedToProduct($this->getEnterpriseProductId())) {
            return true;
        }

        if ($company->subscribedToProduct($this->getGoldProductId())) {
            return $company->projects()->count() < self::MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY;
        }

        if ($company->onTrial()) {
            return $company->projects()->count() < self::MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY;
        }

        return $company->projects()->count() < self::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY;
    }

    public function getMaximumSessionLength(Company $company): int
    {
        if ($company->subscribedToProduct($this->getEnterpriseProductId())) {
            return 600;
        }

        if ($company->subscribedToProduct($this->getGoldProductId())) {
            return 300;
        }

        if ($company->onTrial()) {
            return 300;
        }

        return 90;
    }

    public function getGoldProductId(): string
    {
        return config('stripe.products.gold.id');
    }

    public function getEnterpriseProductId(): string
    {
        return config('stripe.products.enterprise.id');
    }

    public function getEnterpriseMonthlyPricingId(): string
    {
        return config('stripe.products.enterprise.prices.monthly');
    }

    public function getGoldMonthlyPricingId(): string
    {
        return config('stripe.products.gold.prices.monthly');
    }

    public function isGoldProduct(string $productId): bool
    {
        return $productId === $this->getEnterpriseProductId();
    }

    public function isEnterpriseProduct(string $productId): bool
    {
        return $productId === $this->getEnterpriseProductId();
    }

    public function isSubscribedToGoldPlan(Company $company): bool
    {
        return $company->subscribedToProduct($this->getGoldProductId());
    }

    public function isSubscribedToEnterprisePlan(Company $company): bool
    {
        return $company->subscribedToProduct($this->getEnterpriseProductId());
    }

    public function isPayingCustomer(Company $company): bool
    {
        return $company->onTrial()
            || $this->isSubscribedToGoldPlan($company)
            || $this->isSubscribedToEnterprisePlan($company);
    }
}
