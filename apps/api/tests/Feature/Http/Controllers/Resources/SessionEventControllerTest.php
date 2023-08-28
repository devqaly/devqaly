<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Enum\Event\EventLogLevelEnum;
use App\Models\Session\Event;
use App\Models\Session\Session;
use App\Models\User;
use Database\Factories\Session\Event\EventNetworkRequestFactory;
use Database\Factories\Session\EventFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SessionEventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_company_member_can_see_session_events(): void
    {
        $session = Session::factory()->create();

        $createPayload = ['session_id' => $session->id];

        $databaseTransactionEvent = Event::factory()->databaseTransaction()->create($createPayload);
        $clickElementEvent = Event::factory()->clickElementEvent()->create($createPayload);
        $scrollEvent = Event::factory()->scrollEvent()->create($createPayload);
        $logEvent = Event::factory()->logEvent()->create($createPayload);
        $networkRequestEvent = Event::factory()->networkRequestEvent()->create($createPayload);
        $resizeScreenEvent = Event::factory()->resizeScreenEvent()->create($createPayload);
        $urlChangeEvent = Event::factory()->urlChangeEvent()->create($createPayload);

        Sanctum::actingAs($session->project->company->createdBy, ['*']);

        $response = $this
            ->getJson(route('sessions.events.index', [
                'projectSession' => $session
            ]))
            ->assertOk()
            ->assertJsonPath('meta.total', 7)
            ->assertJsonCount(7, 'data');

        $eventsResponse = collect(json_decode($response->getContent(), true)['data']);

        $clickElementResponse = $eventsResponse->firstOrFail(function (array $event) {
            return $event['type'] === Event\EventElementClick::class;
        });

        $this->assertEquals($clickElementResponse['id'], $clickElementEvent->id);
        $this->assertEquals($clickElementResponse['source'], $clickElementEvent->source);
        $this->assertEquals($clickElementResponse['event']['positionX'], $clickElementEvent->eventable->position_x);
        $this->assertEquals($clickElementResponse['event']['positionY'], $clickElementEvent->eventable->position_y);
        $this->assertEquals($clickElementResponse['event']['elementClasses'], $clickElementEvent->eventable->element_classes);
        $this->assertEquals($clickElementResponse['event']['innerText'], $clickElementEvent->eventable->inner_text);

        $databaseTransactionResponse = $eventsResponse->firstOrFail(function (array $event) {
            return $event['type'] === Event\EventDatabaseTransaction::class;
        });

        $this->assertEquals($databaseTransactionResponse['id'], $databaseTransactionEvent->id);
        $this->assertEquals($databaseTransactionResponse['source'], $databaseTransactionEvent->source);
        $this->assertEquals($databaseTransactionResponse['event']['sql'], $databaseTransactionEvent->eventable->sql);
        $this->assertEquals($databaseTransactionResponse['event']['executionTimeInMilliseconds'], $databaseTransactionEvent->eventable->execution_time_in_milliseconds);
        $this->assertEquals($databaseTransactionResponse['event']['requestId'], $databaseTransactionEvent->eventable->request_id);

        $scrollResponse = $eventsResponse->firstOrFail(function (array $event) {
            return $event['type'] === Event\EventElementScroll::class;
        });

        $this->assertEquals($scrollResponse['id'], $scrollEvent->id);
        $this->assertEquals($scrollResponse['source'], $scrollEvent->source);
        $this->assertEquals($scrollResponse['event']['scrollHeight'], $scrollEvent->eventable->scroll_height);
        $this->assertEquals($scrollResponse['event']['scrollWidth'], $scrollEvent->eventable->scroll_width);
        $this->assertEquals($scrollResponse['event']['scrollLeft'], $scrollEvent->eventable->scroll_left);
        $this->assertEquals($scrollResponse['event']['scrollTop'], $scrollEvent->eventable->scroll_top);

        $logResponse = $eventsResponse->firstOrFail(function (array $event) {
            return $event['type'] === Event\EventLog::class;
        });

        $this->assertEquals($logResponse['id'], $logEvent->id);
        $this->assertEquals($logResponse['source'], $logEvent->source);
        $this->assertEquals($logResponse['event']['level'], $logEvent->eventable->level);
        $this->assertEquals($logResponse['event']['log'], $logEvent->eventable->log);
        $this->assertEquals($logResponse['event']['requestId'], $logEvent->eventable->request_id);

        $networkEventResponse = $eventsResponse->firstOrFail(function (array $event) {
            return $event['type'] === Event\EventNetworkRequest::class;
        });

        $this->assertEquals($networkEventResponse['id'], $networkRequestEvent->id);
        $this->assertEquals($networkEventResponse['source'], $networkRequestEvent->source);
        $this->assertEquals($networkEventResponse['event']['method'], $networkRequestEvent->eventable->method);
        $this->assertEquals($networkEventResponse['event']['url'], $networkRequestEvent->eventable->url);
        $this->assertEquals($networkEventResponse['event']['requestHeaders'], json_decode($networkRequestEvent->eventable->request_headers, true));
        $this->assertEquals($networkEventResponse['event']['requestBody'], $networkRequestEvent->eventable->request_body);
        $this->assertEquals($networkEventResponse['event']['responseHeaders'], json_decode($networkRequestEvent->eventable->response_headers, true));
        $this->assertEquals($networkEventResponse['event']['responseBody'], $networkRequestEvent->eventable->response_body);
        $this->assertEquals($networkEventResponse['event']['responseStatus'], $networkRequestEvent->eventable->response_status);
        $this->assertEquals($networkEventResponse['event']['requestId'], $networkRequestEvent->eventable->request_id);

        $resizeResponse = $eventsResponse->firstOrFail(function (array $event) {
            return $event['type'] === Event\EventResizeScreen::class;
        });

        $this->assertEquals($resizeResponse['id'], $resizeScreenEvent->id);
        $this->assertEquals($resizeResponse['source'], $resizeScreenEvent->source);
        $this->assertEquals($resizeResponse['event']['innerHeight'], $resizeScreenEvent->eventable->inner_height);
        $this->assertEquals($resizeResponse['event']['innerWidth'], $resizeScreenEvent->eventable->inner_width);


        $urlResponse = $eventsResponse->firstOrFail(function (array $event) {
            return $event['type'] === Event\EventUrlChanged::class;
        });

        $this->assertEquals($urlResponse['id'], $urlChangeEvent->id);
        $this->assertEquals($urlResponse['source'], $urlChangeEvent->source);
        $this->assertEquals($urlResponse['event']['toUrl'], $urlChangeEvent->eventable->to_url);

    }

    public function test_non_company_member_cant_see_session_events(): void
    {
        $session = Session::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['*']);

        $this
            ->getJson(route('sessions.events.index', [
                'projectSession' => $session
            ]))
            ->assertForbidden();
    }

    public function test_company_member_can_create_database_transaction_event(): void
    {
        $session = Session::factory()->create();

        $payload = [
            ...$this->baseCreateEventPayload(Event\EventDatabaseTransaction::class),
            'sql' => 'select * from users where 1 = 1',
            'executionTimeInMilliseconds' => $this->faker->numberBetween(1, 500),
            'requestId' => null,
            'securityToken' => $session->project->security_token
        ];

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload,
                ['x-devqaly-session-secret-token' => $session->secret_token]
            )
            ->assertNoContent();

        $this->assertDatabaseHas((new Event\EventDatabaseTransaction())->getTable(), [
            'sql' => $payload['sql'],
            'execution_time_in_milliseconds' => $payload['executionTimeInMilliseconds'],
            'request_id' => $payload['requestId']
        ]);

        $databaseTransactionEvent = Event\EventDatabaseTransaction::query()->firstOrFail();

        $this->assertDatabaseHas((new Event())->getTable(), [
            'session_id' => $session->id,
            'event_type' => Event\EventDatabaseTransaction::class,
            'event_id' => $databaseTransactionEvent->id,
            'client_utc_event_created_at' => $payload['clientUtcEventCreatedAt']
        ]);
    }

    public function test_company_member_can_create_element_click_event(): void
    {
        $session = Session::factory()->create();

        $payload = [
            ...$this->baseCreateEventPayload(Event\EventElementClick::class),
            'positionX' => rand(1, 1500),
            'positionY' => rand(1, 1500),
            'elementClasses' => $this->faker->words(rand(1, 10), true),
            'innerText' => $this->faker->words(rand(1, 10), true),
        ];

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload,
                ['x-devqaly-session-secret-token' => $session->secret_token]
            )
            ->assertNoContent();

        $this->assertDatabaseHas((new Event\EventElementClick())->getTable(), [
            'position_x' => $payload['positionX'],
            'position_y' => $payload['positionY'],
            'element_classes' => $payload['elementClasses'],
            'inner_text' => $payload['innerText'],
        ]);

        $clickEvent = Event\EventElementClick::query()->firstOrFail();

        $this->assertDatabaseHas((new Event())->getTable(), [
            'session_id' => $session->id,
            'event_type' => Event\EventElementClick::class,
            'event_id' => $clickEvent->id,
            'client_utc_event_created_at' => $payload['clientUtcEventCreatedAt']
        ]);
    }

    public function test_company_member_can_create_element_scroll_event(): void
    {
        $session = Session::factory()->create();

        $payload = [
            ...$this->baseCreateEventPayload(Event\EventElementScroll::class),
            'scrollHeight' => rand(1, 1500),
            'scrollWidth' => rand(1, 1500),
            'scrollLeft' => rand(1, 1500),
            'scrollTop' => rand(1, 1500),
        ];

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload,
                ['x-devqaly-session-secret-token' => $session->secret_token]
            )
            ->assertNoContent();

        $this->assertDatabaseHas((new Event\EventElementScroll())->getTable(), [
            'scroll_height' => $payload['scrollHeight'],
            'scroll_width' => $payload['scrollWidth'],
            'scroll_left' => $payload['scrollLeft'],
            'scroll_top' => $payload['scrollTop'],
        ]);

        $scrollEvent = Event\EventElementScroll::query()->firstOrFail();

        $this->assertDatabaseHas((new Event())->getTable(), [
            'session_id' => $session->id,
            'event_type' => Event\EventElementScroll::class,
            'event_id' => $scrollEvent->id,
            'client_utc_event_created_at' => $payload['clientUtcEventCreatedAt']
        ]);
    }

    public function test_company_member_can_create_log_event(): void
    {
        $session = Session::factory()->create();

        $payload = [
            ...$this->baseCreateEventPayload(Event\EventLog::class),
            'log' => $this->faker->words(rand(10, 500), true),
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
            'requestId' => null,
        ];

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload,
                ['x-devqaly-session-secret-token' => $session->secret_token]
            )
            ->assertNoContent();

        $this->assertDatabaseHas((new Event\EventLog())->getTable(), [
            'log' => $payload['log'],
            'level' => $payload['level'],
            'request_id' => $payload['requestId'],
        ]);

        $logEvent = Event\EventLog::query()->firstOrFail();

        $this->assertDatabaseHas((new Event())->getTable(), [
            'session_id' => $session->id,
            'event_type' => Event\EventLog::class,
            'event_id' => $logEvent->id,
            'client_utc_event_created_at' => $payload['clientUtcEventCreatedAt']
        ]);
    }

    public function test_company_member_can_create_network_request_event(): void
    {
        $session = Session::factory()->create();

        $payload = [
            ...$this->baseCreateEventPayload(Event\EventNetworkRequest::class),
            'method' => $this->faker->randomElement(EventNetworkRequestFactory::HTTP_VERBS),
            'url' => $this->faker->url,
            'requestId' => Str::uuid()->toString(),
            'responseStatus' => $this->faker->randomElement([200, 201, 400, 401]),
            'requestHeaders' => '{ "Authorization": "Bearer 2owqi210213bjqweij" }',
            'requestBody' => '{ "foo": "bar" }',
            'responseHeaders' => '{ "Server": "apache" }',
            'responseBody' => '{ "foo": "bar" }',
        ];

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload,
                ['x-devqaly-session-secret-token' => $session->secret_token]
            )
            ->assertNoContent();

        $this->assertDatabaseHas((new Event\EventNetworkRequest())->getTable(), [
            'method' => $payload['method'],
            'url' => $payload['url'],
            'request_id' => $payload['requestId'],
            'response_status' => $payload['responseStatus'],
            'request_headers' => $payload['requestHeaders'],
            'request_body' => $payload['requestBody'],
            'response_headers' => $payload['responseHeaders'],
            'response_body' => $payload['responseBody'],
        ]);

        $networkEvent = Event\EventNetworkRequest::query()->firstOrFail();

        $this->assertDatabaseHas((new Event())->getTable(), [
            'session_id' => $session->id,
            'event_type' => Event\EventNetworkRequest::class,
            'event_id' => $networkEvent->id,
            'client_utc_event_created_at' => $payload['clientUtcEventCreatedAt']
        ]);
    }

    public function test_company_member_can_create_resize_screen_event(): void
    {
        $session = Session::factory()->create();

        $payload = [
            ...$this->baseCreateEventPayload(Event\EventResizeScreen::class),
            'innerWidth' => rand(1500, 2000),
            'innerHeight' => rand(1500, 2000),
        ];

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload,
                ['x-devqaly-session-secret-token' => $session->secret_token]
            )
            ->assertNoContent();

        $this->assertDatabaseHas((new Event\EventResizeScreen())->getTable(), [
            'inner_height' => $payload['innerHeight'],
            'inner_width' => $payload['innerWidth'],
        ]);

        $resizeEvent = Event\EventResizeScreen::query()->firstOrFail();

        $this->assertDatabaseHas((new Event())->getTable(), [
            'session_id' => $session->id,
            'event_type' => Event\EventResizeScreen::class,
            'event_id' => $resizeEvent->id,
            'client_utc_event_created_at' => $payload['clientUtcEventCreatedAt']
        ]);
    }

    public function test_company_member_can_create_url_change_event(): void
    {
        $session = Session::factory()->create();

        $payload = [
            ...$this->baseCreateEventPayload(Event\EventUrlChanged::class),
            'toUrl' => $this->faker->url,
        ];

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload,
                ['x-devqaly-session-secret-token' => $session->secret_token]
            )
            ->assertNoContent();

        $this->assertDatabaseHas((new Event\EventUrlChanged())->getTable(), [
            'to_url' => $payload['toUrl'],
        ]);

        $urlChangedEvent = Event\EventUrlChanged::query()->firstOrFail();

        $this->assertDatabaseHas((new Event())->getTable(), [
            'session_id' => $session->id,
            'event_type' => Event\EventUrlChanged::class,
            'event_id' => $urlChangedEvent->id,
            'client_utc_event_created_at' => $payload['clientUtcEventCreatedAt']
        ]);
    }

    public function test_not_able_to_create_event_when_incorrect_secret_is_passed(): void
    {
        $session = Session::factory()->create();

        $payload = [
            ...$this->baseCreateEventPayload(Event\EventUrlChanged::class),
            'toUrl' => $this->faker->url,
        ];

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload,
                ['x-devqaly-session-secret-token' => 'incorrect secret']
            )
            ->assertForbidden();

        $this
            ->postJson(
                route('sessions.events.store', ['projectSession' => $session]),
                $payload
            )
            ->assertForbidden();
    }

    private function baseCreateEventPayload(string $type): array
    {
        return [
            'source' => $this->faker->randomElement(EventFactory::FAKE_SOURCES),
            'clientUtcEventCreatedAt' => now()->format('Y-m-d\TH:i:s.u\Z'),
            'type' => $type,
        ];
    }
}
