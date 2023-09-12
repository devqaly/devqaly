<?php

use App\Enum\Subscription\SubscriptionIdentifiersEnum;
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
                $company->createOrGetStripeCustomer([
                    'name' => $company->name,
                    'email' => $company->createdBy->email,
                ]);
            });
        });
    }

    public function down(): void
    {

    }
};
