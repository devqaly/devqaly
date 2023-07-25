<?php

namespace tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a user is able to log out
     *
     * @return void
     */
    public function test_user_can_logout_out()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('authentication.logout'));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
