<?php

namespace tests\Feature\Http\Controllers\Auth;

use app\Enum\User\UserCurrentPositionEnum;
use App\Mail\Auth\SignupEmail;
use App\Models\Auth\RegisterToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
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
    // @TODO: uncomment this when we allow users to sign in manually in the app
//    public function test_user_is_able_to_create_register_token()
//    {
//        Mail::fake();
//
//        $email = $this->faker->email;
//
//        Mail::assertNothingQueued();
//
//        $response = $this->postJson(route('registerTokens.store'), [
//            'email' => $email
//        ]);
//
//        Mail::assertQueued(SignupEmail::class);
//
//        $response->assertStatus(Response::HTTP_NO_CONTENT);
//        $this->assertDatabaseHas((new RegisterToken())->getTable(), [
//            'email' => $email
//        ]);
//    }

    /**
     * Test user is not able to create two register token with the same email.
     * Table "register_tokens" should only have two equal emails in the table
     * if all of them have `revoked` set to `false`, excluding the last one.
     *
     * @return void
     * @group resources
     */
    // @TODO: uncomment this when we allow users to sign in manually in the app
//    public function test_user_is_not_able_to_create_two_register_token_with_same_email()
//    {
//        Mail::fake();
//
//        $email = $this->faker->email;
//
//        RegisterToken::factory()->unrevoked()->create(['email' => $email]);
//
//        Mail::assertNothingQueued();
//
//        $response = $this->postJson(route('registerTokens.store'), [
//            'email' => $email
//        ]);
//
//        Mail::assertNotQueued(SignupEmail::class);
//
//        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
//        $this->assertDatabaseCount((new RegisterToken())->getTable(), 1);
//    }

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

        $response = $this->postJson(route('registerTokens.resendEmail'), [
            'email' => $email
        ]);

        Mail::assertQueued(SignupEmail::class);

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

        $response = $this->postJson(route('registerTokens.resendEmail'), [
            'email' => $email
        ]);

        Mail::assertNothingQueued();

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

        $token = RegisterToken::factory()->unrevoked()->create(['email' => $email]);

        $response = $this->putJson(route('registerTokens.update', ['registerToken' => $token]), [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'timezone' => $timezone,
            'password' => $password,
            'currentPosition' => UserCurrentPositionEnum::QA
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.user.email', $email);

        $this->assertDatabaseHas((new User())->getTable(), [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'timezone' => $timezone,
        ]);
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

        $response = $this->putJson(route('registerTokens.update', ['registerToken' => $token]), [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'timezone' => $timezone,
            'password' => $password
        ]);

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

        $response = $this->putJson(route('registerTokens.update', ['registerToken' => $token]), [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'timezone' => $timezone,
            'password' => $password
        ]);

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
            (bool) RegisterToken::find($token->id)->revoked,
            'The old token should be revoked while a new one should be created'
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseCount((new User())->getTable(), 0);
    }
}
