<?php

namespace Database\Factories\Session\Event;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session\Event\EventNetworkRequest>
 */
class EventNetworkRequestFactory extends Factory
{
    const HTTP_VERBS = [
        'GET',
        'POST',
        'DELETE',
        'PATCH',
        'PUT',
        'OPTIONS',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'method' => $this->faker->randomElement(self::HTTP_VERBS),
            'url' => $this->faker->url,
            'request_id' => Str::uuid()->toString(),
            'response_status' => 200,
            'request_headers' => '{ "Authorization": "Bearer 2owqi210213bjqweij" }',
            'request_body' => '{ "foo": "bar" }',
            'response_headers' => '{ "server": "apache" }',
            'response_body' => '{ "bar": "foo" }',
        ];
    }
}
