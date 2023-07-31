<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create();

        Event::fake();

        $this
            ->postJson(route('password.requestResetLink'), ['email' => $user->email])
            ->assertNoContent();
    }

    public function test_log_is_created_when_email_doesnt_exist_in_db()
    {
        $email = $this->faker->email;

        Event::fake();

        Log::shouldReceive('info')->once();

        $this
            ->postJson(route('password.requestResetLink'), ['email' => $email])
            ->assertNoContent();
    }
}
