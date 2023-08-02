<?php

namespace Database\Factories\Auth;

use App\Models\Auth\RegisterToken;
use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\services\Auth\RegisterTokenService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auth\RegisterToken>
 */
class RegisterTokenFactory extends Factory
{
    private RegisterTokenService $registerTokenService;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

        $this->registerTokenService = app()->make(RegisterTokenService::class);
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->email,
            'token' => $this->registerTokenService->generateToken(),
            'used_at' => null,
            'revoked' => 0,
        ];
    }

    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => now()->subDays(10),
            ];
        });
    }

    public function used() {
        return $this->state(function (array $attributes) {
            return [
                'used_at' => now(),
            ];
        });
    }

    public function revoked() {
        return $this->state(function (array $attributes) {
            return [
                'revoked' => 1,
            ];
        });
    }

    public function unrevoked() {
        return $this->state(function (array $attributes) {
            return [
                'revoked' => 0,
            ];
        });
    }

    public function withCompanyMember(?Company $company): RegisterTokenFactory
    {
        return $this->afterCreating(function (RegisterToken $registerToken) use ($company) {
                CompanyMember::factory()->create([
                    'member_id' => null,
                    'register_token_id' => $registerToken->id,
                    'company_id' => $company?->id ?? Company::factory(),
                ]);
            });
    }
}
