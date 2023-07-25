<?php

namespace Database\Factories\Company;

use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
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
}
