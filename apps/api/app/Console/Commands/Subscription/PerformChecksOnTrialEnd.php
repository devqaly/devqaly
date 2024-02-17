<?php

namespace App\Console\Commands\Subscription;

use App\Enum\Company\CompanyBlockedReasonEnum;
use App\Models\Company\Company;
use App\services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PerformChecksOnTrialEnd extends Command
{
    protected $signature = 'app:perform-checks-on-trial-end';

    protected $description = 'Block companies that terminated their trial without transitioning to a paid plan';

    protected array $reasons = [];

    public function handle(SubscriptionService $subscriptionService): void
    {
        if (config('devqaly.isSelfHosting')) return;

        $now = now();
        $thirtyMinutesAhead = now()->addMinutes(30);

        Company::query()
            ->whereBetween('trial_ends_at', [$now, $thirtyMinutesAhead])
            ->chunk(100, function (Collection $companies) use ($subscriptionService) {
                /** @var Company $company */
                foreach ($companies as $company) {
                    // User can only be subscribed to Gold or Enterprise plan.
                    // The checks for resources we do in each resource's controller.
                    if ($company->subscribed()) continue;

                    if ($subscriptionService->hasMoreMembersThanAllowedOnFreePlan($company)) {
                        $this->addHasMoreMembersToReasonsToBeBlocked();
                    }

                    if ($subscriptionService->hasMoreProjectsThanAllowedOnFreePlan($company)) {
                        $this->addHasMoreProjectsToReasonsToBeBlocked();
                    }

                    $this->markCompanyAsBlocked($company);
                }
            });

    }

    private function addHasMoreMembersToReasonsToBeBlocked(): void
    {
        $this->reasons[] = [
            'reason' => CompanyBlockedReasonEnum::TRIAL_FINISHED_AND_HAS_MORE_MEMBERS_THAN_ALLOWED_ON_FREE_PLAN->value,
            'description' => 'Your free trial have ended and you have more members than it is allowed on free plan',
            'possibleResolution' => 'You can either sign up to a paid plan or remove members from your company'
        ];
    }

    private function addHasMoreProjectsToReasonsToBeBlocked(): void
    {
        $this->reasons[] = [
            'reason' => CompanyBlockedReasonEnum::TRIAL_FINISHED_AND_HAS_MORE_PROJECTS_THAN_ALLOWED_ON_FREE_PLAN->value,
            'description' => 'Your free trial have ended and you have more projects than it is allowed on free plan',
            'possibleResolution' => 'You can either sign up to a paid plan or remove projects from your company'
        ];
    }

    private function markCompanyAsBlocked(Company $company): void
    {
        if (empty($this->reasons)) return;

        Log::info(sprintf('Blocking company %s with trial ending at %s', $company->id, $company->trial_ends_at));

        $company->blocked_reasons = $this->reasons;
        $company->save();
    }
}
