<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
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

        /** @var Company $company */
        $company = Company::query()->firstOrFail();

        $this->assertDatabaseCount((new Company())->getTable(), 1);
        $this->assertFalse($company->subscribed());
    }

    public function test_stripe_client_is_not_called_on_self_hosted_instance_when_creating_company()
    {
        $spy = $this->spy(Company::class)->makePartial();

        $spy->shouldReceive('newInstance')->andReturn($spy);

        $loggedUser = User::factory()->create();

        $companyName = $this->faker->company();

        Sanctum::actingAs($loggedUser, ['*']);

        Config::set('devqaly.isSelfHosting', true);

        $this
            ->postJson(route('companies.store'), [
                'name' => $companyName
            ])
            ->assertCreated();

        $this->assertDatabaseHas((new Company())->getTable(), [
            'name' => $companyName
        ]);

        $this->assertDatabaseCount((new Company())->getTable(), 1);
        $spy->shouldNotReceive('createOrGetStripeCustomer');

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
