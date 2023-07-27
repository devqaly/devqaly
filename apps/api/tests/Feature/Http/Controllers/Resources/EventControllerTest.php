<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Company\Company;
use App\Models\Project\Project;
use App\Models\Session\Event;
use App\Models\Session\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_non_company_member_cant_see_database_transactions_by_request_id(): void
    {
        $networkRequest = Event::factory()->networkRequestEvent()->create();

        $nonCompanyMember = Company::factory()->create()->createdBy;

        Sanctum::actingAs($nonCompanyMember, ['*']);

        $this
            ->getJson(route('eventNetworkRequest.byRequestId.databaseTransactions', [
                'networkRequestEvent' => $networkRequest->eventable->request_id,
            ]))
            ->assertForbidden();
    }

    public function test_company_member_can_see_database_transactions_by_request_id(): void
    {
        $company = Company::factory()->create();
        $project = Project::factory()->create(['company_id' => $company->id]);
        $session = Session::factory()->create(['created_by_id' => $company->created_by_id, 'project_id' => $project->id]);

        $networkRequest = Event::factory()->networkRequestEvent()->create(['session_id' => $session->id]);
        $databaseTransaction = Event::factory()->databaseTransaction()->create(['session_id' => $session->id]);

        $databaseTransaction->eventable->update(['request_id' => $networkRequest->eventable->request_id,]);

        Sanctum::actingAs($company->createdBy, ['*']);

        $this
            ->getJson(route('eventNetworkRequest.byRequestId.databaseTransactions', [
                'networkRequestEvent' => $networkRequest->eventable->request_id,
            ]))
            ->assertOk()
            ->assertJsonPath('data.0.id', $databaseTransaction->id)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonCount(1, 'data');

    }
}
