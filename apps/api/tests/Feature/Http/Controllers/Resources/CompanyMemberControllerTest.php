<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Mail\Auth\SignupEmail;
use App\Models\Auth\RegisterToken;
use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyMemberControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_company_member_can_list_company_members(): void
    {
        $numberMembers = rand(2, 5);
        $company = Company::factory()->withMembers($numberMembers)->create();

        Sanctum::actingAs($company->createdBy, ['*']);

        $this
            ->getJson(route('companyMembers.index', [
                'company' => $company
            ]))
            ->assertOk()
            ->assertJsonPath('meta.total', $company->members()->count())
            ->assertJsonCount($company->members()->count(), 'data');
    }

    public function test_non_company_member_cant_fetch_members()
    {
        $numberMembers = rand(2, 5);
        $company = Company::factory()->withMembers($numberMembers)->create();

        Sanctum::actingAs(User::factory()->create(), ['*']);

        $this
            ->getJson(route('companyMembers.index', [
                'company' => $company
            ]))
            ->assertForbidden();
    }

    public function test_company_member_can_invite_member()
    {
        $company = Company::factory()->create();
        $companyMember = $company->createdBy;
        $email = $this->faker->unique()->email();

        Sanctum::actingAs($companyMember, ['*']);

        Mail::fake();

        Mail::assertNothingOutgoing();

        $this
            ->postJson(route('companyMembers.store', ['company' => $company]), [
                'emails' => [$email]
            ])
            ->assertNoContent();

        $this->assertDatabaseHas((new RegisterToken())->getTable(), [
            'email' => $email,
            'revoked' => false,
        ]);


        Mail::assertQueued(SignupEmail::class, function (SignupEmail $signupEmail) use ($email) {
            return $signupEmail->hasTo($email);
        });
    }

    public function test_company_member_cant_invite_same_member_twice()
    {
        $company = Company::factory()->withMembers()->create();
        $companyMember = $company->members->random()->first()->member;
        $email = $companyMember->email;

        Sanctum::actingAs($companyMember, ['*']);

        Mail::fake();

        Mail::assertNothingOutgoing();

        $this
            ->postJson(route('companyMembers.store', ['company' => $company]), [
                'emails' => [$email]
            ])
            ->assertNoContent();

        Mail::assertNothingOutgoing();

    }

    public function test_non_company_member_cant_invite_member()
    {
        $company = Company::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['*']);

        Mail::fake();

        Mail::assertNothingOutgoing();

        $this
            ->postJson(route('companyMembers.store', ['company' => $company]), [
                'emails' => [$this->faker->email()]
            ])
            ->assertForbidden();

        Mail::assertNothingOutgoing();

        $this->assertDatabaseCount((new RegisterToken())->getTable(), 0);
    }
}
