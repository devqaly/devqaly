<?php

use App\Models\Company\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration
{
    public function up(): void
    {
        if (config('devqaly.isSelfHosting')) {
            return;
        }

        Company::query()->chunk(100, function (Collection $companies) {
            $companies->each(function (Company $company) {
                $stripeCustomer = $company->createOrGetStripeCustomer([
                    'name' => $company->name,
                    'email' => $company->createdBy->email,
                ]);

                $company->newSubscription('freemium', 'price_1NmbwXGaq6OMdWB2XZSiENQx')->create();
            });
        });
    }

    public function down(): void
    {

    }
};
