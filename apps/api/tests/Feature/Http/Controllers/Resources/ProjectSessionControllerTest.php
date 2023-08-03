<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Project\Project;
use App\Models\Session\Session;
use App\Models\User;
use Database\Factories\Session\SessionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            ->assertJsonPath('data.platformName', $sessionPayload['platformName'])
            ->assertJsonPath('data.version', $sessionPayload['version'])
            ->assertJsonPath('data.windowWidth', $sessionPayload['windowWidth'])
            ->assertJsonPath('data.windowHeight', $sessionPayload['windowHeight']);

        $this->assertDatabaseCount((new Session())->getTable(), 1);
    }

    private function createSessionPayload(): array
    {
        $windowDimensions = $this->faker->randomElement([
            ...SessionFactory::WINDOW_HEIGHTS['16:9'],
            ...SessionFactory::WINDOW_HEIGHTS['16:10'],
        ]);

        return [
            'os' => $this->faker->randomElement(SessionFactory::OPERATING_SYSTEMS),
            'platformName' => $this->faker->randomElement(SessionFactory::PLATFORM_NAMES),
            'version' => $this->faker->numberBetween(100, 200) . '.0.0',
            'windowWidth' => $windowDimensions[0],
            'windowHeight' => $windowDimensions[1]
        ];
    }
}
