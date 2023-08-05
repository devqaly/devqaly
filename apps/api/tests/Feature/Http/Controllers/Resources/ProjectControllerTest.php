<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Company\Company;
use App\Models\Project\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_company_member_can_list_projects_for_company(): void
    {
        $project = Project::factory()->create();

        $loggedUser = $project->company->createdBy;

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->getJson(route('companies.projects.index', [
                'company' => $project->company
            ]))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $project->id);
    }

    public function test_non_company_member_cant_list_projects_for_company(): void
    {
        $project = Project::factory()->create();

        $loggedUser = User::factory()->create();

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->getJson(route('companies.projects.index', [
                'company' => $project->company
            ]))
            ->assertForbidden();
    }

    public function test_company_member_can_create_project_for_company(): void
    {
        $company = Company::factory()->withMembers(rand(1, 3))->create();

        $projectMember = $company->members->random()->first()->member;

        $projectName = $this->faker->words(2, true);

        Sanctum::actingAs($projectMember, ['*']);

        $this
            ->postJson(route('companies.projects.store', ['company' => $company]), [
                'title' => $projectName
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', $projectName);

        $this->assertDatabaseHas((new Project())->getTable(), [
            'title' => $projectName,
            'company_id' => $company->id,
            'created_by_id' => $projectMember->id,
        ]);
    }

    public function test_non_company_member_cant_create_project_for_company(): void
    {
        $company = Company::factory()->withMembers(rand(1, 3))->create();

        Sanctum::actingAs(User::factory()->create(), ['*']);

        $projectName = $this->faker->words(2, true);

        $this
            ->postJson(route('companies.projects.store', ['company' => $company]), [
                'title' => $projectName
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing((new Project())->getTable(), [
            'title' => $projectName,
        ]);

        $this->assertDatabaseEmpty((new Project())->getTable());
    }
}