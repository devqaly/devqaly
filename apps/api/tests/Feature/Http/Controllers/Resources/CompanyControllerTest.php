<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_logged_user_is_able_to_create_company(): void
    {
        $loggedUser = User::factory()->create();

        $companyName = $this->faker->company();

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->postJson(route('companies.store'), [
                'name' => $companyName
            ])
            ->assertCreated();

        $this->assertDatabaseHas((new Company())->getTable(), [
            'name' => $companyName
        ]);

        $this->assertDatabaseCount((new Company())->getTable(), 1);
    }

    public function test_logged_user_cant_create_more_than_x_number_companies(): void
    {
        $loggedUser = User::factory()->create();

        Company::factory()->count(Company::MAX_NUMBER_COMPANIES_PER_USER)->create([
            'created_by_id' => $loggedUser->id,
        ]);

        $companyName = $this->faker->company();

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->postJson(route('companies.store'), [
                'name' => $companyName
            ])
            ->assertForbidden();

        $this->assertDatabaseCount((new Company())->getTable(), Company::MAX_NUMBER_COMPANIES_PER_USER);
    }
}