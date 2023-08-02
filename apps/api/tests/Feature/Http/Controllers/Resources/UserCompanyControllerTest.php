<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserCompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_user_can_get_companies_hes_member(): void
    {
        $company = Company::factory()->create();

        $loggedUser = $company->createdBy;

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->getJson(route('users.companies.index', [
                'user' => $loggedUser
            ]))
            ->assertOk()
            ->assertJsonPath('data.0.id', $company->id)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 1);

    }

    public function test_third_party_user_cant_companies_other_user_is_member()
    {
        $company = Company::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['*']);

        $this
            ->getJson(route('users.companies.index', [
                'user' => $company->createdBy
            ]))
            ->assertForbidden();
    }
}
