<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Enum\Company\CompanyBlockedReasonEnum;
use App\Mail\Auth\SignupEmail;
use App\Models\Auth\RegisterToken;
use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
use App\services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\Support\UsesSubscriptionTrait;
use Tests\TestCase;

class CompanyMemberControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, UsesSubscriptionTrait;

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
        $emails = $this->generateEmails(4);

        Sanctum::actingAs($companyMember, ['*']);

        Mail::fake();

        Mail::assertNothingOutgoing();

        $this
            ->postJson(route('companyMembers.store', ['company' => $company]), [
                'emails' => $emails
            ])
            ->assertNoContent();

        $this->postCheckEmails($emails);
    }

    public function test_company_member_cant_invite_same_member_twice()
    {
        $company = Company::factory()->withMembers(2)->create();
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

    public function test_free_company_cant_invite_more_than_freemium_allows(): void
    {
        $company = Company::factory()
            ->withMembers(SubscriptionService::MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY)
            ->create();

        $companyMember = $company->createdBy;
        $email = $this->faker->unique()->email();

        Sanctum::actingAs($companyMember, ['*']);

        Mail::fake();

        Mail::assertNothingOutgoing();

        $this
            ->postJson(route('companyMembers.store', ['company' => $company]), [
                'emails' => [$email]
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing((new RegisterToken())->getTable(), ['email' => $email]);

        Mail::assertNothingOutgoing();
    }

    public function test_company_on_enterprise_plan_can_invite_members(): void
    {
        $membersSize = rand(50, 100);
        $companyEnterprise = Company::factory()->withMembers($membersSize)->create();

        $companyMemberEnterprise = $companyEnterprise->createdBy;
        $email = sprintf('bruno.francisco.%s@devqaly.com', substr(Str::uuid()->toString(), -5));

        $this->createSubscriptionForCompany(
            $companyEnterprise,
            $this->subscriptionService->getEnterpriseMonthlyPricingId()
        );

        Sanctum::actingAs($companyMemberEnterprise, ['*']);

        Mail::fake();

        Mail::assertNothingOutgoing();

        $this
            ->postJson(route('companyMembers.store', ['company' => $companyEnterprise]), ['emails' => [$email]])
            ->assertNoContent();

        $this->assertDatabaseHas((new RegisterToken())->getTable(), [
            'email' => $email,
            'revoked' => false,
        ]);

        Mail::assertQueued(SignupEmail::class, function (SignupEmail $signupEmail) use ($email) {
            return $signupEmail->hasTo($email);
        });

        $this->assertEquals(
        // "+ 2" because we need to include the user that created the company PLUS the user that just got invited
            $membersSize + 2,
            $companyEnterprise
                ->subscription()
                ->usageRecordsFor($this->subscriptionService->getEnterpriseMonthlyPricingId())
                ->reduce(fn($carry, $item) => $carry + $item['total_usage'], 0)
        );
    }

    public function test_company_on_gold_plan_can_invite_members(): void
    {
        $membersSize = rand(50, 100);
        $companyGold = Company::factory()->withMembers($membersSize)->create();

        $companyMemberEnterprise = $companyGold->createdBy;
        $email = sprintf('bruno.francisco.%s@devqaly.com', substr(Str::uuid()->toString(), -5));

        $this->createSubscriptionForCompany(
            $companyGold,
            $this->subscriptionService->getGoldMonthlyPricingId()
        );

        Sanctum::actingAs($companyMemberEnterprise, ['*']);

        Mail::fake();

        Mail::assertNothingOutgoing();

        $this
            ->postJson(route('companyMembers.store', ['company' => $companyGold]), ['emails' => [$email]])
            ->assertNoContent();

        $this->assertDatabaseHas((new RegisterToken())->getTable(), [
            'email' => $email,
            'revoked' => false,
        ]);

        Mail::assertQueued(SignupEmail::class, function (SignupEmail $signupEmail) use ($email) {
            return $signupEmail->hasTo($email);
        });

        $this->assertEquals(
        // "+ 2" because we need to include the user that created the company PLUS the user that just got invited
            $membersSize + 2,
            $companyGold
                ->subscription()
                ->usageRecordsFor($this->subscriptionService->getGoldMonthlyPricingId())
                ->reduce(fn($carry, $item) => $carry + $item['total_usage'], 0)
        );
    }

    public function test_self_hosted_version_does_not_have_subscription_concerns(): void
    {
        $spy = $this->spy(SubscriptionService::class)->makePartial();

        $company = Company::factory()->create();
        $companyMember = $company->createdBy;
        $emails = $this->generateEmails(6);

        Config::set('devqaly.isSelfHosting', true);

        Sanctum::actingAs($companyMember, ['*']);

        Mail::fake();

        Mail::assertNothingOutgoing();

        $this
            ->postJson(route('companyMembers.store', ['company' => $company]), [
                'emails' => $emails
            ])
            ->assertNoContent();

        $this->postCheckEmails($emails);

        $spy->shouldNotReceive('isPayingCustomer');
        $spy->shouldNotReceive('isSubscribedToGoldPlan');
        $spy->shouldNotReceive('isSubscribedToEnterprisePlan');
    }

    public function test_non_company_user_cant_delete_company_members(): void
    {
        $numberCompanyMembers = 2;
        $company = Company::factory()->withMembers($numberCompanyMembers)->create();

        $companyMember = $company->members->random()->first()->member;

        Sanctum::actingAs(User::factory()->create());

        $this
            ->postJson(route('companyMembers.removeMembers', ['company' => $company]), [
                'users' => [$companyMember->id],
            ])
            ->assertForbidden();

        // "+ 1" because the owner of the company is added to the CompanyMember table
        $this->assertDatabaseCount((new CompanyMember())->getTable(), $numberCompanyMembers + 1);
    }

    public function test_company_must_have_at_least_one_members(): void
    {
        $company = Company::factory()->create();

        Sanctum::actingAs($company->createdBy);

        $this
            ->postJson(route('companyMembers.removeMembers', ['company' => $company]), [
                'users' => [$company->createdBy->id],
            ])
            ->assertForbidden();

        $this->assertDatabaseCount((new CompanyMember())->getTable(), 1);

        $company = Company::factory()->withMembers()->create();

        Sanctum::actingAs($company->createdBy);

        $this
            ->postJson(route('companyMembers.removeMembers', ['company' => $company]), [
                'users' => $company->members->map(fn(CompanyMember $companyMember) => $companyMember->member_id)
            ])
            ->assertForbidden()
            ->assertJsonPath('message', 'You must have at least 1 members in the company');
    }

    public function test_company_user_can_delete_company_members(): void
    {
        $numberCompanyMembers = 2;
        $company = Company::factory()->withMembers($numberCompanyMembers)->create();

        /** @var Collection $companyMembers */
        $companyMembers = $company->members->random($numberCompanyMembers);

        $registerToken = RegisterToken::factory()
            ->withCompanyMember($company)
            ->create();

        Sanctum::actingAs($company->createdBy);

        $this
            ->postJson(route('companyMembers.removeMembers', ['company' => $company]), [
                'users' => $companyMembers->map(fn(CompanyMember $companyMember) => $companyMember->member_id),
                'registerTokens' => [$registerToken->id],
            ])
            ->assertNoContent();

        $this->assertDatabaseCount((new CompanyMember())->getTable(), 1);
    }

    public function test_blocked_reason_gets_removed_if_it_has_one()
    {
        /** @var Company $company */
        $company = Company::factory()
            ->withBlockedReasons([
                CompanyBlockedReasonEnum::TRIAL_FINISHED_AND_HAS_MORE_MEMBERS_THAN_ALLOWED_ON_FREE_PLAN,
                CompanyBlockedReasonEnum::TRIAL_FINISHED_AND_HAS_MORE_PROJECTS_THAN_ALLOWED_ON_FREE_PLAN,
            ])
            // -1 because the owner of the company will count as a member
            ->withMembers(SubscriptionService::MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY - 1)
            ->create();

        /** @var Collection $invitedMember */
        $invitedMember = $company
            ->members
            ->first(function (CompanyMember $companyMember) use ($company) {
                return $companyMember->member_id !== $company->created_by_id;
            });

        Sanctum::actingAs($company->createdBy);

        $this
            ->postJson(route('companyMembers.removeMembers', ['company' => $company]), [
                'users' => [$invitedMember->member_id],
            ])
            ->assertNoContent();

        $company->refresh();

        $this->assertIsArray($company->blocked_reasons);
        $this->assertCount(1, $company->blocked_reasons);
        $this->assertEquals(
            collect($company->blocked_reasons)->first()['reason'],
            CompanyBlockedReasonEnum::TRIAL_FINISHED_AND_HAS_MORE_PROJECTS_THAN_ALLOWED_ON_FREE_PLAN->value
        );
    }

    private function postCheckEmails(array $emails): void
    {
        foreach ($emails as $email) {
            $this->assertDatabaseHas((new RegisterToken())->getTable(), [
                'email' => $email,
                'revoked' => false,
            ]);


            Mail::assertQueued(SignupEmail::class, function (SignupEmail $signupEmail) use ($email) {
                return $signupEmail->hasTo($email);
            });
        }
    }

    private function generateEmails(int $numberEmails): array
    {
        return collect(array_fill(1, $numberEmails, ''))
            ->map(fn() => $this->faker->unique()->email())
            ->toArray();
    }
}
