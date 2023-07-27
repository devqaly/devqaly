<?php

namespace Database\Factories\Session\Event;

use App\Enum\Event\EventLogLevelEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session\Event\EventLog>
 */
class EventLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_id' => null,
            'level' => $this->faker->randomElement([
                EventLogLevelEnum::EMERGENCY->value,
                EventLogLevelEnum::ALERT->value,
                EventLogLevelEnum::CRITICAL->value,
                EventLogLevelEnum::ERROR->value,
                EventLogLevelEnum::WARNING->value,
                EventLogLevelEnum::NOTICE->value,
                EventLogLevelEnum::INFORMATIONAL->value,
                EventLogLevelEnum::DEBUG->value,
            ]),
            'log' => $this->faker->text(rand(10, 500)),
        ];
    }
}
