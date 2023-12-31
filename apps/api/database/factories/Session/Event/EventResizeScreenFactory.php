<?php

namespace Database\Factories\Session\Event;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session\Event\EventResizeScreen>
 */
class EventResizeScreenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inner_width' => rand(100, 1500),
            'inner_height' => rand(100, 1500),
        ];
    }
}
