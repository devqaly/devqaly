<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_user_can_fetch_his_info(): void
    {
        $loggedUser = User::factory()->create();

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->getJson(route('users.me'))
            ->assertOk()
            ->assertJsonPath('data.id', $loggedUser->id)
            ->assertJsonPath('data.firstName', $loggedUser->first_name)
            ->assertJsonPath('data.lastName', $loggedUser->last_name)
            ->assertJsonPath('data.timezone', $loggedUser->timezone);
    }
}
