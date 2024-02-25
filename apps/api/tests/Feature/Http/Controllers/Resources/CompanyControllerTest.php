<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\Company\Company;
use App\Models\User;
use App\services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
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

        /** @var Company $company */
        $company = Company::query()->firstOrFail();

        $this->assertDatabaseCount((new Company())->getTable(), 1);

        $this->assertTrue($company->onTrial());

        $now = now();

        $this->assertEquals(
            $now->diffInDays($company->trial_ends_at),
            SubscriptionService::SUBSCRIPTION_INITIAL_TRIAL_DAYS - 1
        );
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

    public function test_user_can_update_company_billing_details(): void
    {
        $company = Company::factory()->withMembers()->create();

        $randomMember = $company->members()->where('member_id', '!=', $company->created_by_id)->get()->random()->member;

        $invoiceDetails = 'This is the new billing details';
        $billingContact = $this->faker->safeEmail();

        Sanctum::actingAs($randomMember, ['*']);

        $this
            ->putJson(route('company.updateBillingDetails', ['company' => $company]), [
                'billingContact' => $billingContact
            ])
            ->assertOk()
            ->assertJsonPath('data.billingContact', $billingContact)
            ->assertJsonPath('data.invoiceDetails', null);

        $this->assertDatabaseHas((new Company())->getTable(), [
            'id' => $company->id,
            'billing_contact' => $billingContact,
            'invoice_details' => null,
        ]);

        $this
            ->putJson(route('company.updateBillingDetails', ['company' => $company]), [
                'invoiceDetails' => $invoiceDetails
            ])
            ->assertOk()
            ->assertJsonPath('data.billingContact', $billingContact)
            ->assertJsonPath('data.invoiceDetails', $invoiceDetails);

        $this->assertDatabaseHas((new Company())->getTable(), [
            'id' => $company->id,
            'billing_contact' => $billingContact,
            'invoice_details' => $invoiceDetails
        ]);
    }

    public function test_non_company_member_cant_update_billing_details(): void
    {
        $company = Company::factory()->withMembers()->create();

        $invoiceDetails = 'some details';

        Sanctum::actingAs(User::factory()->create(), ['*']);

        $this
            ->putJson(route('company.updateBillingDetails', ['company' => $company]), [
                'invoiceDetails' => $invoiceDetails
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing((new Company())->getTable(), [
            'id' => $company->id,
            'invoice_details' => $invoiceDetails
        ]);
    }
}
