<?php

namespace Database\Factories\Session\Event;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session\Event\EventElementClick>
 */
class EventElementClickFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'position_x' => rand(800, 1500),
            'position_y' => rand(800, 1500),
            'element_class' => $this->faker->boolean() ? 'text-blue text-lg' : null,
            'inner_text' => $this->faker->boolean() ? $this->faker->text(255) : null,
        ];
    }
}
