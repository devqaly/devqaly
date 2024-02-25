<?php

namespace App\Console\Commands\Deploy;

use App\Models\Company\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CreateCustomersInStripe extends Command
{
    protected $signature = 'app:create-customers-in-stripe';

    protected $description = 'Creates stripe customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('devqaly.isSelfHosting')) return;

        Company::query()
            ->chunk(100, function (Collection $companies) {
                /** @var Company $company */
                foreach ($companies as $company) {
                    $company->createOrGetStripeCustomer([
                        'name' => $company->name,
                        'email' => $company->createdBy->email,
                    ]);
                }

                sleep(1);
            });
    }
}
