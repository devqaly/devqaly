<?php

namespace Database\Factories;

use App\Enum\User\UserCurrentPositionEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => sprintf('bruno-test-%s@devqaly.com', rand(1, 999999)),
            'timezone' => fake()->timezone(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'current_position' => fake()->randomElement([
                UserCurrentPositionEnum::DEVELOPER->value,
                UserCurrentPositionEnum::QA->value,
                UserCurrentPositionEnum::PROJECT_MANAGER->value,
                UserCurrentPositionEnum::MANAGER->value,
                UserCurrentPositionEnum::OTHER->value,
            ])
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
