<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Company\Company;
use App\Models\Project\Project;
use App\Models\Session\Session;
use App\Models\User;
use Database\Factories\Session\SessionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectSessionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_company_member_can_see_sessions_for_project(): void
    {
        $session = Session::factory()->create();

        $loggedUser = $session->project->company->createdBy;

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->getJson(route('projects.sessions.index', [
                'project' => $session->project,
            ]))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $session->id);
    }

    public function test_non_company_member_cant_see_sessions_for_project(): void
    {
        $session = Session::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['*']);

        $this
            ->getJson(route('projects.sessions.index', [
                'project' => $session->project,
            ]))
            ->assertForbidden();
    }

    public function test_user_with_correct_project_key_can_create_session_for_project(): void
    {
        $project = Project::factory()->create();

        $sessionPayload = $this->createSessionPayload();

        $this
            ->postJson(route('projects.sessions.store', [
                'project' => $project
            ]), $sessionPayload)
            ->assertCreated()
            ->assertJsonPath('data.os', $sessionPayload['os'])
            ->assertJsonPath('data.environment', $sessionPayload['environment'])
            ->assertJsonPath('data.platformName', $sessionPayload['platformName'])
            ->assertJsonPath('data.version', $sessionPayload['version'])
            ->assertJsonPath('data.windowWidth', $sessionPayload['windowWidth'])
            ->assertJsonPath('data.windowHeight', $sessionPayload['windowHeight']);

        $this->assertDatabaseCount((new Session())->getTable(), 1);
        $this->assertDatabaseHas((new Session())->getTable(), [
            'environment' => $sessionPayload['environment'],
            'os' => $sessionPayload['os'],
            'platform_name' => $sessionPayload['platformName'],
            'version' => $sessionPayload['version'],
            'window_width' => $sessionPayload['windowWidth'],
            'window_height' => $sessionPayload['windowHeight'],
        ]);
    }

    public function test_session_returns_correct_maximum_session_length_for_company_with_enterprise(): void
    {
        /** @var Project $project */
        $project = Project::factory()->create();

        /** @var Company $company */
        $company = $project->company;

        $company
            ->newSubscription('default')
            ->meteredPrice(config('stripe.products.enterprise.prices.default'))
            ->create(customerOptions: [
                'metadata' => [
                    'environment' => \config('app.env')
                ]
            ]);

        $sessionPayload = $this->createSessionPayload();

        $this
            ->postJson(route('projects.sessions.store', [
                'project' => $project
            ]), $sessionPayload)
            ->assertCreated()
            ->assertJsonPath('meta.maximumSessionLengthInSeconds', 600);
    }

    public function test_session_returns_correct_maximum_session_length_for_company_on_free_plan(): void
    {
        /** @var Project $project */
        $project = Project::factory()->create();

        $sessionPayload = $this->createSessionPayload();

        $this
            ->postJson(route('projects.sessions.store', [
                'project' => $project
            ]), $sessionPayload)
            ->assertCreated()
            ->assertJsonPath('meta.maximumSessionLengthInSeconds', 300);
    }

    public function test_last_x_sessions_are_soft_deleted_when_creating_session_in_freemium_and_not_self_hosting()
    {
        /** @var Project $project */
        $project = Project::factory()->create();

        Session::factory()
            ->count(Session::MAXIMUM_NUMBER_SESSIONS_FOR_FREE_COMPANIES)
            ->create([
                'project_id' => $project->id,
                'created_at' => now()->subMonth()
            ]);

        $sessionPayload = $this->createSessionPayload();

        $this
            ->postJson(route('projects.sessions.store', [
                'project' => $project
            ]), $sessionPayload)
            ->assertCreated();

        $numberSessionsInDatabase = $project->sessions()->orderBy('created_at', 'DESC')->get();

        $this->assertEquals(
            Session::MAXIMUM_NUMBER_SESSIONS_FOR_FREE_COMPANIES,
            $numberSessionsInDatabase->count(),
        );

        $numberTrashedSessionsInDatabase = $project->sessions()->withTrashed()->count();

        $this->assertEquals(
            Session::MAXIMUM_NUMBER_SESSIONS_FOR_FREE_COMPANIES + 1,
            $numberTrashedSessionsInDatabase
        );
    }

    public function test_sessions_are_not_deleted_when_self_hosting()
    {
        /** @var Project $project */
        $project = Project::factory()->create();

        $numberSessions = Session::MAXIMUM_NUMBER_SESSIONS_FOR_FREE_COMPANIES;

        Session::factory()
            ->count($numberSessions)
            ->create([
                'project_id' => $project->id,
                'created_at' => now()->subMonth()
            ]);

        Config::set('devqaly.isSelfHosting', true);

        $sessionPayload = $this->createSessionPayload();

        $this
            ->postJson(route('projects.sessions.store', [
                'project' => $project
            ]), $sessionPayload)
            ->assertCreated();

        $numberSessionsInDatabase = $project->sessions()->orderBy('created_at', 'DESC')->get();

        $this->assertEquals($numberSessions + 1, $numberSessionsInDatabase->count());

        $numberTrashedSessionsInDatabase = $project->sessions()->withTrashed()->count();

        $this->assertEquals($numberSessions + 1, $numberTrashedSessionsInDatabase);
    }

    public function test_sessions_are_not_deleted_when_in_enterprise_plan()
    {
        /** @var Project $project */
        $project = Project::factory()->create();

        $numberSessions = Session::MAXIMUM_NUMBER_SESSIONS_FOR_FREE_COMPANIES;

        Session::factory()
            ->count($numberSessions)
            ->create([
                'project_id' => $project->id,
                'created_at' => now()->subMonth()
            ]);

        /** @var Company $company */
        $company = $project->company;

        $company
            ->newSubscription('default')
            ->meteredPrice(config('stripe.products.enterprise.prices.default'))
            ->create(customerOptions: [
                'metadata' => [
                    'environment' => \config('app.env')
                ]
            ]);

        Config::set('devqaly.isSelfHosting', false);

        $sessionPayload = $this->createSessionPayload();

        $this
            ->postJson(route('projects.sessions.store', [
                'project' => $project
            ]), $sessionPayload)
            ->assertCreated();

        $numberSessionsInDatabase = $project->sessions()->orderBy('created_at', 'DESC')->get();

        $this->assertEquals($numberSessions + 1, $numberSessionsInDatabase->count());

        $numberTrashedSessionsInDatabase = $project->sessions()->withTrashed()->count();

        $this->assertEquals($numberSessions + 1, $numberTrashedSessionsInDatabase);

        // We are only reporting the usage when calling the HTTP endpoint and not when creating the factory
        $this->assertEquals(
            1,
            $company
                ->subscription()
                ->usageRecordsFor(config('stripe.products.enterprise.prices.default'))
                ->reduce(fn ($carry, $item) => $carry + $item['total_usage'], 0)
        );
    }

    private function createSessionPayload(): array
    {
        $windowDimensions = $this->faker->randomElement([
            ...SessionFactory::WINDOW_HEIGHTS['16:9'],
            ...SessionFactory::WINDOW_HEIGHTS['16:10'],
        ]);

        $environment = $this->faker->randomElement(['production', 'staging', 'local', 'development']);

        return [
            'os' => $this->faker->randomElement(SessionFactory::OPERATING_SYSTEMS),
            'platformName' => $this->faker->randomElement(SessionFactory::PLATFORM_NAMES),
            'version' => $this->faker->numberBetween(100, 200) . '.0.0',
            'windowWidth' => $windowDimensions[0],
            'windowHeight' => $windowDimensions[1],
            'environment' => $environment
        ];
    }
}
