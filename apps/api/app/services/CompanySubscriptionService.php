<?php

namespace App\services;

use App\Models\Company\Company;
use Error;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CompanySubscriptionService
{
    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function changeCompanyPlan(Company $company, string $newPlan): void
    {
        if ($newPlan === 'free') {
            $this->handleSwitchToFreePlan($company);
        } else if ($newPlan === 'gold') {
            $this->handleSwitchToGoldPlan($company);
        } else {
            throw new Error("Invalid plan passed: $newPlan");
        }
    }

    private function checksPaymentMethod(Company $company): void
    {
        if (!$company->hasPaymentMethod()) {
            abort(Response::HTTP_FORBIDDEN, "Company must have at least one payment method");
        }
    }

    private function handleSwitchToFreePlan(Company $company): void
    {
        $this->checkCompanyCanDowngradeToFreePlan($company);
        $this->downgradeCompanyToFreePlan($company);
    }

    private function handleSwitchToGoldPlan(Company $company): void
    {
        $this->checksPaymentMethod($company);
        $this->checkCompanyCanDowngradeToGoldPlan($company);
        $this->createSubscriptionForGoldPlan($company);
    }

    private function checkCompanyCanDowngradeToGoldPlan(Company $company): void
    {
        if ($this->subscriptionService->isSubscribedToGoldPlan($company)) {
            abort(Response::HTTP_BAD_REQUEST, "You are already on gold plan");
        }

        $numberProjects = $company->projects()->count();

        if ($numberProjects > SubscriptionService::MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY) {
            $numberProjectsNeededRemoved = (
                $numberProjects - SubscriptionService::MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY
            );

            abort(Response::HTTP_FORBIDDEN, "You have to remove $numberProjectsNeededRemoved projects to downgrade to the gold plan");
        }
    }

    private function checkCompanyCanDowngradeToFreePlan(Company $company): void
    {
        if (is_null($company->subscription())) {
            abort(Response::HTTP_BAD_REQUEST, "You are already on a free plan");
        }

        $numberMembers = $company->members()->count();
        $numberProjects = $company->projects()->count();

        if ($numberMembers > SubscriptionService::MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY) {
            $numberMembersNeededRemoved = (
                $numberMembers - SubscriptionService::MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY
            );

            abort(Response::HTTP_FORBIDDEN, "You have to remove $numberMembersNeededRemoved members to downgrade to the free plan");

        }

        if ($numberProjects > SubscriptionService::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY) {
            $numberProjectsNeededRemoved = (
                $numberProjects - SubscriptionService::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY
            );

            abort(Response::HTTP_FORBIDDEN, "You have to remove $numberProjectsNeededRemoved projects to downgrade to the free plan");
        }
    }

    private function downgradeCompanyToFreePlan(Company $company): void
    {
        $company->subscription()->cancel();

        Log::info("Canceled subscription for company [$company->id]");
    }

    private function createSubscriptionForGoldPlan(Company $company): void
    {
        $pricing = $this->subscriptionService->getGoldMonthlyPricingId();
        $numberMembers = $company->members()->count();

        $subscription = $company
            ->newSubscription('default')
            ->meteredPrice($pricing)
            ->create();

        $subscription->reportUsageFor($pricing, $numberMembers);
    }
}
