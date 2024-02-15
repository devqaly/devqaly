<?php

namespace App\Console\Commands\Subscription;

use App\Models\Company\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PerformChecksOnTrialEnd extends Command
{
    protected $signature = 'app:perform-checks-on-trial-end';

    protected $description = 'Block companies that terminated their trial without transitioning to a paid plan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('devqaly.isSelfHosting')) return;

        $now = now();
        $thirtyMinutesAhead = now()->addMinutes(30);

        Company::query()
            ->whereBetween('trial_ends_at', [$now, $thirtyMinutesAhead])
            ->chunk(100, function (Collection $companies) {
                /** @var Company $company */
                foreach ($companies as $company) {
//                    if (!is_null($company->subscription())) {
//                        return;
//                    }

                    $this->markCompanyAsBlocked($company);
                }
            });
    }

    private function markCompanyAsBlocked(Company $company): void
    {
        Log::info(sprintf('Blocking company %s with trial ending at %s', $company->id, $company->trial_ends_at));

        $company->is_blocked_after_trial = true;
        $company->save();
    }
}
