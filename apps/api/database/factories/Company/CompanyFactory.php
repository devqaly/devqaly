<?php

namespace Database\Factories\Company;

use App\Enum\Company\CompanyBlockedReasonEnum;
use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
use App\services\SubscriptionService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'created_by_id' => User::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Company $company) {
            CompanyMember::create([
                'company_id' => $company->id,
                'member_id' => $company->created_by_id,
                // Currently we do not care about the register_token_id
                'register_token_id' => null,
                'invited_by_id' => $company->created_by_id,
            ]);
        });
    }

    public function withMembers($count = 5): CompanyFactory
    {
        return $this->afterCreating(function (Company $company) use ($count) {
            $users = User::factory()->count($count)->create();

            $users->each(function (User $user) use ($company) {
                CompanyMember::create([
                    'company_id' => $company->id,
                    'member_id' => $user->id,
                    // Currently we do not care about the register_token_id
                    'register_token_id' => null,
                    'invited_by_id' => $company->created_by_id,
                ]);
            });
        });
    }

    public function withTrial(): CompanyFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'trial_ends_at' => now()->addDays(SubscriptionService::SUBSCRIPTION_INITIAL_TRIAL_DAYS)
            ];
        });
    }

    public function withBlockedReasons(array $reasons): CompanyFactory
    {
        return $this->state(function (array $attributes) use ($reasons) {
            return [
                'blocked_reasons' => collect($reasons)->map(fn (CompanyBlockedReasonEnum $reason) => [
                    'reason' => $reason->value,
                    'description' => $this->faker->words(10, true),
                ])->toArray()
            ];
        });
    }
}
