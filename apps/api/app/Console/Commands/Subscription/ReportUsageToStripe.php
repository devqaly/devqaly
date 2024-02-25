<?php

namespace App\Console\Commands\Subscription;

use App\Models\Company\Company;
use App\services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ReportUsageToStripe extends Command
{
    protected $signature = 'app:report-usage-to-stripe';

    protected $description = 'Will report usage to stripe for the current plan';

    public function handle(SubscriptionService $subscriptionService)
    {
        if (config('devqaly.isSelfHosting')) return;

        Company::query()
            ->whereHas('subscriptions')
            ->withCount('members')
            ->whereNotBetween(
                'last_time_reported_usage_to_stripe',
                [now()->startOfMonth(), now()->endOfMonth()]
            )
            ->chunk(150, function (Collection $companies) use ($subscriptionService) {
                /** @var Company $company */
                foreach ($companies as $company) {
                    $company->subscription()->reportUsage($company->members_count);

                    $company->last_time_reported_usage_to_stripe = now();
                    $company->save();

                    sleep(1);
                }
            });
    }
}
