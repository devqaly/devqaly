<?php

namespace tests\Feature\Http\Controllers\Auth;

use app\Enum\User\UserCurrentPositionEnum;
use App\Events\MixpanelEventCreated;
use App\Mail\Auth\SignupEmail;
use App\Models\Auth\RegisterToken;
use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\Project\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegisterTokenControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * Test user is able to create register token
     *
     * @return void
     * @group resources
     */
    public function test_user_is_able_to_create_register_token()
    {
        Mail::fake();
        Event::fake([MixpanelEventCreated::class]);

        $email = $this->faker->email;

        Mail::assertNothingQueued();

        $response = $this->postJson(route('registerTokens.store'), [
            'email' => $email
        ]);

        Mail::assertQueued(SignupEmail::class);
        Event::assertDispatched(function (MixpanelEventCreated $event) {
            return $event->eventName === 'register-token-created';
        });

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseHas((new RegisterToken())->getTable(), [
            'email' => $email,
            'has_onboarding' => true
        ]);
    }

    /**
     * Test user is not able to create two register token with the same email.
     * Table "register_tokens" should only have two equal emails in the table
     * if all of them have `revoked` set to `false`, excluding the last one.
     *
     * @return void
     * @group resources
     */
    public function test_user_is_not_able_to_create_two_register_token_with_same_email()
    {
        Mail::fake();

        $email = $this->faker->email;

        RegisterToken::factory()->unrevoked()->create(['email' => $email]);

        Mail::assertNothingQueued();
        Event::fake([MixpanelEventCreated::class]);

        $response = $this->postJson(route('registerTokens.store'), [
            'email' => $email
        ]);

        Mail::assertNotQueued(SignupEmail::class);
        Event::assertNotDispatched(MixpanelEventCreated::class);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseCount((new RegisterToken())->getTable(), 1);
    }

    /**
     * Currently, the onboarding process requires the user to have a Project and a Company.
     * Whenever we create a RegisterToken, if the user is registering for the first time
     * (meaning he is not being invited to be a member of a company) he will go through
     * the onboarding process. For that, we need to create the Company and Project.
     *
     * @return void
     * @group resources
     */
    public function test_register_token_that_has_onboarding_creates_company_and_project_on_self_hosted_version() {

        $email = $this->faker->email;
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        $token = RegisterToken::factory()
            ->hasOnboarding()
            ->unrevoked()
            ->create(['email' => $email]);

        Config::set('devqaly.isSelfHosting', true);

        Event::fake([MixpanelEventCreated::class]);

        $response = $this
            ->putJson(route('registerTokens.update', ['registerToken' => $token]), [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'timezone' => $timezone,
                'password' => $password,
                'currentPosition' => 'qa'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.user.email', $email);

        Event::assertDispatched(function (MixpanelEventCreated $event) use ($token) {
            return $event->eventName === 'finished-registration' && $event->email === $token->email;
        });

        $user = User::query()->where('email', $email)->firstOrFail();
        $company = Company::query()->where('created_by_id', $user->id)->firstOrFail();
        $project = Project::query()->where('company_id', $company->id)->where('created_by_id', $user->id)->firstOrFail();

        $response = json_decode($response->getContent(), true)['data'];

        $this->assertEquals($response['company']['id'], $company->id);
        $this->assertEquals($response['project']['id'], $project->id);
        $this->assertTrue($response['registerToken']['hasOnboarding']);

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'member_id' => $user->id,
            'company_id' => $company->id
        ]);
    }

    /**
     * Currently, the onboarding process requires the user to have a Project and a Company.
     * Whenever we create a RegisterToken, if the user is registering for the first time
     * (meaning he is not being invited to be a member of a company) he will go through
     * the onboarding process. For that, we need to create the Company and Project.
     *
     * @return void
     * @group resources
     */
    public function test_register_token_that_has_onboarding_creates_company_and_project_on_cloud_version() {

        $email = $this->faker->email;
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        $token = RegisterToken::factory()
            ->hasOnboarding()
            ->unrevoked()
            ->create(['email' => $email]);

        Config::set('devqaly.isSelfHosting', false);

        Event::fake([MixpanelEventCreated::class]);

        $response = $this
            ->putJson(route('registerTokens.update', ['registerToken' => $token]), [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'timezone' => $timezone,
                'password' => $password,
                'currentPosition' => 'qa'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.user.email', $email);

        Event::assertDispatched(function (MixpanelEventCreated $event) use ($token) {
            return $event->eventName === 'finished-registration' && $event->email === $token->email;
        });

        $user = User::query()->where('email', $email)->firstOrFail();
        $company = Company::query()->where('created_by_id', $user->id)->firstOrFail();
        $project = Project::query()->where('company_id', $company->id)->where('created_by_id', $user->id)->firstOrFail();

        $response = json_decode($response->getContent(), true)['data'];

        $this->assertEquals($response['company']['id'], $company->id);
        $this->assertEquals($response['project']['id'], $project->id);
        $this->assertTrue($response['registerToken']['hasOnboarding']);

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'member_id' => $user->id,
            'company_id' => $company->id
        ]);
    }

    /**
     * Test user is able to send re-send email when registering on the first step
     *
     * @return void
     * @group resources
     */
    public function test_user_is_able_to_send_email_for_already_created_register_token()
    {
        Mail::fake();

        $email = $this->faker->email;

        RegisterToken::factory()->unrevoked()->create(['email' => $email]);

        Mail::assertNothingQueued();
        Event::fake([MixpanelEventCreated::class]);

        $response = $this->postJson(route('registerTokens.resendEmail'), [
            'email' => $email
        ]);

        Mail::assertQueued(SignupEmail::class);
        Event::assertDispatched(function (MixpanelEventCreated $event) {
            return $event->eventName === 'register-token-resend-email';
        });

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseCount((new RegisterToken())->getTable(), 2);
    }

    /**
     * Test user receives 204 NO CONTENT when passing an email that doesn't exist in DB
     *
     * @return void
     * @group resources
     */
    public function test_user_receives_204_when_passing_email_that_doesnt_exist_in_database()
    {
        Mail::fake();

        $email = $this->faker->email;

        Mail::assertNothingQueued();
        Event::fake(MixpanelEventCreated::class);

        $response = $this->postJson(route('registerTokens.resendEmail'), [
            'email' => $email
        ]);

        Mail::assertNothingQueued();
        Event::assertNotDispatched(MixpanelEventCreated::class);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseCount((new RegisterToken())->getTable(), 0);
    }

    /**
     * Test user is able to update RegisterToken, therefore finishing the process of
     * creating his account.
     *
     * @return void
     * @group resources
     */
    public function test_user_is_able_to_update_register_token()
    {
        $email = $this->faker->email;
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        $company = Company::factory()->create();

        $token = RegisterToken::factory()
            ->withCompanyMember($company)
            ->unrevoked()
            ->create(['email' => $email]);

        Event::fake([MixpanelEventCreated::class]);

        $this
            ->putJson(route('registerTokens.update', ['registerToken' => $token]), [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'timezone' => $timezone,
                'password' => $password,
                'currentPosition' => UserCurrentPositionEnum::QA
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.user.email', $email);

        Event::assertDispatched(function (MixpanelEventCreated $event) use ($token) {
            return $event->eventName === 'finished-registration' && $event->email === $token->email;
        });

        $this->assertDatabaseHas((new User())->getTable(), [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'timezone' => $timezone,
        ]);

        $token->refresh();

        $user = User::query()->where('email', $email)->firstOrFail();

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'member_id' => $user->id,
            'register_token_id' => $token->id,
        ]);

        $this->assertNotNull($token->used_at, 'The token should have been marked as been used');

    }

    /**
     * Test user is NOT able to update RegisterToken that is revoked
     *
     * @return void
     * @group resources
     */
    public function test_user_is_not_able_to_update_revoked_register_token()
    {
        $email = $this->faker->email;
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        $token = RegisterToken::factory()->revoked()->create(['email' => $email]);

        Event::fake([MixpanelEventCreated::class]);

        $response = $this->putJson(route('registerTokens.update', ['registerToken' => $token]), [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'timezone' => $timezone,
            'password' => $password
        ]);

        Event::assertNotDispatched(MixpanelEventCreated::class);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseCount((new User())->getTable(), 0);
    }

    /**
     * Test user is NOT able to update RegisterToken that is used
     *
     * @return void
     * @group resources
     */
    public function test_user_is_not_able_to_update_used_register_token()
    {
        $email = $this->faker->email;
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        $token = RegisterToken::factory()->used()->create(['email' => $email]);

        Event::fake([MixpanelEventCreated::class]);

        $response = $this->putJson(route('registerTokens.update', ['registerToken' => $token]), [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'timezone' => $timezone,
            'password' => $password
        ]);

        Event::assertNotDispatched(MixpanelEventCreated::class);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseCount((new User())->getTable(), 0);
    }

    /**
     * Test user receives an email when he tries to update an expired token
     *
     * @return void
     * @group resources
     */
    public function test_user_receives_an_email_when_he_tries_to_update_an_expired_token()
    {
        Mail::fake();

        $email = $this->faker->email;
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        $token = RegisterToken::factory()->expired()->create(['email' => $email]);

        Mail::assertNothingQueued();

        $response = $this->putJson(route('registerTokens.update', ['registerToken' => $token]), [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'timezone' => $timezone,
            'password' => $password
        ]);

        Mail::assertQueued(SignupEmail::class);

        $this->assertTrue(
            (bool)RegisterToken::find($token->id)->revoked,
            'The old token should be revoked while a new one should be created'
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseCount((new User())->getTable(), 0);
    }

    public function test_user_is_attached_to_company_after_being_sent_new_email_after_trying_to_use_expired_token()
    {
        Mail::fake();

        $email = $this->faker->email;
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        $company = Company::factory()->create();

        $token = RegisterToken::factory()
            ->withCompanyMember($company)
            ->unrevoked()
            ->expired()
            ->create(['email' => $email]);

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $company->id,
            'register_token_id' => $token->id,
        ]);

        Mail::assertNothingQueued();

        $this
            ->putJson(route('registerTokens.update', ['registerToken' => $token]), [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'timezone' => $timezone,
                'password' => $password,
                'currentPosition' => UserCurrentPositionEnum::QA
            ])
            ->assertForbidden()
            ->assertJsonPath('message', 'Current token have expired. We have sent a new token to the email associated with this token');

        Mail::assertQueued(SignupEmail::class);

        $token->refresh();

        $this->assertTrue($token->revoked, 'The token should be revoked since it was expired');

        $this->assertDatabaseHas((new RegisterToken())->getTable(), [
            'email' => $email,
        ]);

        $this->assertDatabaseCount((new RegisterToken())->getTable(), 2);

        $newToken = RegisterToken::query()->orderBy('created_at', 'DESC')->first();

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $company->id,
            'register_token_id' => $newToken->id
        ]);

        $this
            ->putJson(route('registerTokens.update', ['registerToken' => $newToken]), [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'timezone' => $timezone,
                'password' => $password,
                'currentPosition' => UserCurrentPositionEnum::QA
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.user.email', $email);

        $user = User::query()->where('email', $email)->first();

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $company->id,
            'register_token_id' => $newToken->id,
            'member_id' => $user->id,
        ]);

        $newToken->refresh();

        $this->assertNotNull($newToken->used_at, 'The token should have been marked as been used');
    }

    public function test_user_gets_added_to_x_companies_hes_been_invited_to()
    {
        Mail::fake();

        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $email = $this->faker->email;

        $registerToken = RegisterToken::factory()->unrevoked()->create([
            'email' => $email
        ]);

        CompanyMember::factory()->create([
            'member_id' => null,
            'company_id' => $companyA->id,
            'register_token_id' => $registerToken->id,
            'invited_by_id' => $companyA->created_by_id
        ]);

        Sanctum::actingAs($companyB->createdBy, ['*']);

        // We invite the user to a new company
        $this
            ->postJson(route('companyMembers.store', ['company' => $companyB]), [
                'emails' => [$email]
            ])
            ->assertNoContent();

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $companyB->id,
            'register_token_id' => $registerToken->id,
            'member_id' => null,
        ]);

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        // We finish registering the account as the user
        $this
            ->putJson(route('registerTokens.update', ['registerToken' => $registerToken]), [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'timezone' => $timezone,
                'password' => $password,
                'currentPosition' => UserCurrentPositionEnum::QA
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.user.email', $email);

        $user = User::query()->where('email', $email)->first();

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $companyB->id,
            'register_token_id' => $registerToken->id,
            'member_id' => $user->id,
        ]);

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $companyA->id,
            'register_token_id' => $registerToken->id,
            'member_id' => $user->id,
        ]);

        $registerToken->refresh();

        $this->assertNotNull($registerToken->used_at, 'The `used_at` should be set to when the token was used');
    }

    public function test_user_gets_added_to_x_companies_hes_been_invited_to_when_first_token_got_expired()
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $email = $this->faker->email;

        $registerToken = RegisterToken::factory()
            ->expired()
            ->create([
                'email' => $email
            ]);

        CompanyMember::factory()->create([
            'member_id' => null,
            'company_id' => $companyA->id,
            'register_token_id' => $registerToken->id,
            'invited_by_id' => $companyA->created_by_id
        ]);

        CompanyMember::factory()->create([
            'member_id' => null,
            'company_id' => $companyB->id,
            'register_token_id' => $registerToken->id,
            'invited_by_id' => $companyA->created_by_id
        ]);

        Mail::fake();

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;
        $password = 'password123';

        Mail::assertNothingQueued();

        // We finish registering the account as the user
        // This should fail because the current token is expired
        $this
            ->putJson(route('registerTokens.update', ['registerToken' => $registerToken]), [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'timezone' => $timezone,
                'password' => $password,
                'currentPosition' => UserCurrentPositionEnum::QA
            ])
            ->assertForbidden()
            ->assertJsonPath('message', 'Current token have expired. We have sent a new token to the email associated with this token');

        Mail::assertQueued(SignupEmail::class);

        $registerToken->refresh();

        $this->assertTrue($registerToken->revoked, 'Token should have been revoked');

        $newRegisterToken = RegisterToken::query()
            ->where('revoked', false)
            ->where('email', $email)
            ->firstOrFail();

        // After making the first request the token gets extended.
        // Now we can make a new request.
        $this
            ->putJson(route('registerTokens.update', ['registerToken' => $newRegisterToken]), [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'timezone' => $timezone,
                'password' => $password,
                'currentPosition' => UserCurrentPositionEnum::QA
            ])
            ->assertOk()
            ->assertJsonPath('data.user.email', $email);


        $user = User::query()->where('email', $email)->first();

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $companyB->id,
            'register_token_id' => $newRegisterToken->id,
            'member_id' => $user->id,
        ]);

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $companyA->id,
            'register_token_id' => $newRegisterToken->id,
            'member_id' => $user->id,
        ]);

        $newRegisterToken->refresh();

        $this->assertNotNull($newRegisterToken->used_at, 'The `used_at` should be set to when the token was used');

    }
}
