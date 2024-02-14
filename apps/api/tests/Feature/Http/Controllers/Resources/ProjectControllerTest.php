<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Company\Company;
use App\Models\Project\Project;
use App\Models\User;
use App\services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
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

    public function test_subscription_limits_check_should_not_be_called_on_self_hosting(): void
    {
        $spy = $this->spy(SubscriptionService::class)->makePartial();

        $company = Company::factory()
            ->withMembers(1)
            ->create();

        $projectMember = $company
            ->members
            ->random()
            ->first()
            ->member;

        Project::factory()
            ->count(SubscriptionService::MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY)
            ->create(['company_id' => $company->id]);

        Config::set('devqaly.isSelfHosting', true);

        $projectName = $this->faker->words(2, true);

        Sanctum::actingAs($projectMember, ['*']);

        $this
            ->postJson(route('companies.projects.store', ['company' => $company]), [
                'title' => $projectName
            ])
            ->assertCreated();

        $this->assertDatabaseHas((new Project())->getTable(), [
            'company_id' => $company->id,
            'title' => $projectName
        ]);

        $spy->shouldNotReceive('canCreateProject');
    }

    public function test_company_on_trial_cant_create_more_than_x_projects_per_company(): void
    {
        $company = Company::factory()
            ->withMembers(1)
            ->withTrial()
            ->create();

        $projectMember = $company
            ->members
            ->random()
            ->first()
            ->member;

        Project::factory()
            ->count(SubscriptionService::MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY)
            ->create(['company_id' => $company->id]);

        $projectName = $this->faker->words(2, true);

        Sanctum::actingAs($projectMember, ['*']);

        $this
            ->postJson(route('companies.projects.store', ['company' => $company]), [
                'title' => $projectName
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing((new Project())->getTable(), [
            'company_id' => $company->id,
            'title' => $projectName
        ]);
    }

    public function test_subscription_gold_on_trial_cant_create_more_than_x_projects_per_company(): void
    {
        $company = Company::factory()
            ->withMembers(1)
            ->create();

        $projectMember = $company
            ->members
            ->random()
            ->first()
            ->member;

        Project::factory()
            ->count(SubscriptionService::MAXIMUM_NUMBER_PROJECTS_GOLD_PLAN_PER_COMPANY)
            ->create(['company_id' => $company->id]);

        $this->createStripeCustomerAndAddTrial(
            $company,
            config('stripe.products.gold.prices.monthly'),
            true,
        );

        $projectName = $this->faker->words(2, true);

        Sanctum::actingAs($projectMember, ['*']);

        $this
            ->postJson(route('companies.projects.store', ['company' => $company]), [
                'title' => $projectName
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing((new Project())->getTable(), [
            'company_id' => $company->id,
            'title' => $projectName
        ]);
    }

    public function test_subscription_enterprise_can_create_more_than_x_projects_per_company(): void
    {
        $company = Company::factory()
            ->withMembers(1)
            ->create();

        $projectMember = $company
            ->members
            ->random()
            ->first()
            ->member;

        Project::factory()
            ->count(500)
            ->create(['company_id' => $company->id]);

        $this->createStripeCustomerAndAddTrial(
            $company,
            config('stripe.products.enterprise.prices.default'),
            false
        );

        $projectName = $this->faker->words(2, true);

        Sanctum::actingAs($projectMember, ['*']);

        $this
            ->postJson(route('companies.projects.store', ['company' => $company]), [
                'title' => $projectName
            ])
            ->assertCreated();

        $this->assertDatabaseHas((new Project())->getTable(), [
            'company_id' => $company->id,
            'title' => $projectName
        ]);
    }

    public function test_subscription_free_cant_create_more_than_x_projects_per_company(): void
    {
        $company = Company::factory()
            ->withMembers(1)
            ->create();

        $projectMember = $company
            ->members
            ->random()
            ->first()
            ->member;

        Project::factory()
            ->count(SubscriptionService::MAXIMUM_NUMBER_PROJECTS_FREE_PLAN_PER_COMPANY)
            ->create(['company_id' => $company->id]);

        $projectName = $this->faker->words(2, true);

        Sanctum::actingAs($projectMember, ['*']);

        $this
            ->postJson(route('companies.projects.store', ['company' => $company]), [
                'title' => $projectName
            ])
            ->assertForbidden()
            ->assertJsonPath('message', 'You have exceed the amount of projects for this plan');

        $this->assertDatabaseMissing((new Project())->getTable(), [
            'company_id' => $company->id,
            'title' => $projectName
        ]);
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

        $project = Project::query()->where('title', $projectName)->firstOrFail();

        $this->assertDatabaseHas((new Project())->getTable(), [
            'title' => $projectName,
            'company_id' => $company->id,
            'created_by_id' => $projectMember->id,
            'security_token' => $project->security_token,
            'project_key' => $project->project_key
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

    public function test_non_company_member_cant_revoke_project_security_token(): void
    {
        $project = Project::factory()->create();

        $loggedUser = User::factory()->create();

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->putJson(route('projects.securityToken.update', [
                'project' => $project
            ]))
            ->assertForbidden();
    }

    public function test_company_member_can_revoke_project_security_token(): void
    {
        $project = Project::factory()->create();

        $oldSecurityToken = $project->security_token;

        Sanctum::actingAs($project->company->createdBy, ['*']);

        $response = $this
            ->putJson(route('projects.securityToken.update', [
                'project' => $project
            ]))
            ->assertOk()
            ->assertJsonPath('data.id', $project->id);

        $this->assertDatabaseMissing((new Project())->getTable(), [
            'security_token' => $oldSecurityToken
        ]);

        $response = json_decode($response->getContent(), true)['data'];

        $project->refresh();

        $this->assertEquals($response['securityToken'], $project->security_token);
    }

    private function createStripeCustomerAndAddTrial(
        Company $company,
        string  $pricing,
        bool    $hasTrial
    ): void
    {
        $company->createOrGetStripeCustomer([
            'email' => $company->createdBy->email,
            'name' => $company->name
        ]);

        if ($hasTrial) {
            $company
                ->newSubscription('default', $pricing)
                ->trialDays(SubscriptionService::SUBSCRIPTION_INITIAL_TRIAL_DAYS)
                // Quantity is necessary to set to null on metered plans
                // @see https://stackoverflow.com/a/64613077/4581336
                ->quantity(null)
                ->create();
        } else {
            $company
                ->newSubscription('default', $pricing)
                // Quantity is necessary to set to null on metered plans
                // @see https://stackoverflow.com/a/64613077/4581336
                ->quantity(null)
                ->create();
        }
    }
}
