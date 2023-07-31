<?php

namespace Database\Factories\Session;

use App\Models\Session\Event\EventDatabaseTransaction;
use App\Models\Session\Event\EventElementClick;
use App\Models\Session\Event\EventElementScroll;
use App\Models\Session\Event\EventLog;
use App\Models\Session\Event\EventNetworkRequest;
use App\Models\Session\Event\EventResizeScreen;
use App\Models\Session\Event\EventUrlChanged;
use App\Models\Session\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session\Event>
 */
class EventFactory extends Factory
{
    const FAKE_SOURCES = [
        'laravel-sdk',
        'backend',
        'microservice-1',
        'microservice-2',
        'browser-sdk'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session_id' => Session::factory(),
            'source' => $this->faker->randomElement(self::FAKE_SOURCES),
            'client_utc_event_created_at' => now(),
        ];
    }

    public function databaseTransaction(): EventFactory
    {
        return $this->state(function () {
            return [
                'event_type' => EventDatabaseTransaction::class,
                'event_id' => EventDatabaseTransaction::factory(),
            ];
        });
    }

    public function clickElementEvent(): EventFactory
    {
        return $this->state(function () {
            return [
                'event_type' => EventElementClick::class,
                'event_id' => EventElementClick::factory(),
            ];
        });
    }

    public function scrollEvent(): EventFactory
    {
        return $this->state(function () {
            return [
                'event_type' => EventElementScroll::class,
                'event_id' => EventElementScroll::factory(),
            ];
        });
    }

    public function logEvent(): EventFactory
    {
        return $this->state(function () {
            return [
                'event_type' => EventLog::class,
                'event_id' => EventLog::factory(),
            ];
        });
    }

    public function networkRequestEvent(): EventFactory
    {
        return $this->state(function () {
            return [
                'event_type' => EventNetworkRequest::class,
                'event_id' => EventNetworkRequest::factory(),
            ];
        });
    }

    public function resizeScreenEvent(): EventFactory
    {
        return $this->state(function () {
            return [
                'event_type' => EventResizeScreen::class,
                'event_id' => EventResizeScreen::factory(),
            ];
        });
    }

    public function urlChangeEvent(): EventFactory
    {
        return $this->state(function () {
            return [
                'event_type' => EventUrlChanged::class,
                'event_id' => EventUrlChanged::factory(),
            ];
        });
    }
}
