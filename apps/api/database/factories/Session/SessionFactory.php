<?php

namespace Database\Factories\Session;

use App\Enum\Sessions\SessionVideoStatusEnum;
use App\Models\Project\Project;
use App\Models\Session\Event;
use App\Models\Session\Session;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session\Session>
 */
class SessionFactory extends Factory
{
    const WINDOW_HEIGHTS = [
        '16:9' => [
            [1024, 576],
            [1280, 720],
            [1920, 1080],
        ],
        '16:10' => [
            [1680, 1050],
            [1920, 1200],
        ]
    ];

    const OPERATING_SYSTEMS = [
        'MacOS',
        'Windows',
        'Linux',
    ];

    const PLATFORM_NAMES = [
        'Chrome',
        'Firefox',
        'Safari',
        'Firefox',
    ];

    const VERSIONS = [
        '114.0.0',
        '113.0.0',
        '98.0.0',
        '98.0.1'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $windowDimensions = $this->faker->randomElement([
            ...self::WINDOW_HEIGHTS['16:9'],
            ...self::WINDOW_HEIGHTS['16:10'],
        ]);

        return [
            'created_by_id' => User::factory(),
            'project_id' => Project::factory(),
            'window_width' => $windowDimensions[0],
            'window_height' => $windowDimensions[1],
            'os' => $this->faker->randomElement(self::OPERATING_SYSTEMS),
            'platform_name' => $this->faker->randomElement(self::PLATFORM_NAMES),
            'version' => $this->faker->randomElement(self::VERSIONS),
            'video_status' => SessionVideoStatusEnum::CONVERTED->value,
            'video_extension' => 'webm',
            'video_size_in_megabytes' => rand(1000, 5000),
            'video_conversion_percentage' => 100,
            'video_duration_in_seconds' => rand(10, 1500),
            'started_video_conversion_at' => now(),
            'ended_video_conversion_at' => now()->addSeconds(rand(1, 5)),
            'secret_token' => Str::random(60),
        ];
    }

    public function unassigned(): SessionFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'created_by_id' => null,
            ];
        });
    }

    public function convertedVideoStatus(): SessionFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'video_status' => SessionVideoStatusEnum::CONVERTED->value,
            ];
        });
    }

    public function inQueueForConversionVideoStatus(): SessionFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'video_status' => SessionVideoStatusEnum::IN_QUEUE_FOR_CONVERSION->value,
            ];
        });
    }

    public function withOneEventForEachType(): SessionFactory
    {
        return $this->afterCreating(function (Session $session) {
            $clientEventCreatedAt = $session->created_at->clone()->addSeconds(1);

            Event::factory()->databaseTransaction()->create([
                'session_id' => $session->id,
                'client_utc_event_created_at' => $clientEventCreatedAt
            ]);

            Event::factory()->clickElementEvent()->create([
                'session_id' => $session->id,
                'client_utc_event_created_at' => $clientEventCreatedAt
            ]);

            Event::factory()->scrollEvent()->create([
                'session_id' => $session->id,
                'client_utc_event_created_at' => $clientEventCreatedAt
            ]);

            Event::factory()->logEvent()->create([
                'session_id' => $session->id,
                'client_utc_event_created_at' => $clientEventCreatedAt
            ]);

            Event::factory()->networkRequestEvent()->create([
                'session_id' => $session->id,
                'client_utc_event_created_at' => $clientEventCreatedAt
            ]);

            Event::factory()->resizeScreenEvent()->create([
                'session_id' => $session->id,
                'client_utc_event_created_at' => $clientEventCreatedAt
            ]);

            Event::factory()->urlChangeEvent()->create([
                'session_id' => $session->id,
                'client_utc_event_created_at' => $clientEventCreatedAt
            ]);
        });
    }
}
