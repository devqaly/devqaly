<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a user is able to log in given right credentials
     *
     * @return void
     */
    public function test_user_can_login_into_application_given_right_credentials()
    {
        $rawPassword = 'password';
        $password = Hash::make($rawPassword);
        $user = User::factory()->create(['password' => $password]);

        $response = $this->postJson(route('authentication.login'), [
            'email' => $user->email,
            'password' => $rawPassword,
            'tokenName' => 'Chrome (Linux)'
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    /**
     * Test a user is NOT able to log in given wrong credentials
     *
     * @return void
     */
    public function test_user_can_not_login_into_application_given_wrong_credentials()
    {
        $rawPassword = 'password';
        $password = Hash::make($rawPassword);
        $user = User::factory()->create(['password' => $password]);

        $response = $this->postJson(route('authentication.login'), [
            'email' => $user->email,
            'password' => 'totally-wrong-password',
            'tokenName' => 'Chrome (Linux)'
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
