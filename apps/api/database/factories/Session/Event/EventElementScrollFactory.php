<?php

namespace Database\Factories\Session\Event;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session\Event\EventElementScroll>
 */
class EventElementScrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scroll_height' => rand(500, 1500),
            'scroll_width' => rand(500, 1500),
            'scroll_left' => rand(0, 1500),
            'scroll_top' => rand(0, 1500),
        ];
    }
}
