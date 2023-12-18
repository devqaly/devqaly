<?php

namespace Tests\Feature\Http\Controllers\Resources;

use App\Enum\S3NamespacesEnum;
use App\Jobs\Session\ProcessSessionVideo;
use App\Models\Session\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_member_can_see_session(): void
    {
        $session = Session::factory()->create();

        Sanctum::actingAs($session->project->company->createdBy, ['*']);

        $this
            ->getJson(route('sessions.show', [
                'session' => $session
            ]))
            ->assertOk()
            ->assertJsonPath('data.id', $session->id);
    }

    public function test_non_company_member_cant_see_full_session(): void
    {
        $session = Session::factory()->create();

        $loggedUser = User::factory()->create(['first_name' => 'bellon']);

        Sanctum::actingAs($loggedUser, ['*']);

        $this
            ->getJson(route('sessions.show', [
                'session' => $session
            ]))
            ->assertOk()
            ->assertJsonPath('data.id', $session->id)
            ->assertJsonPath('data.project.id', $session->project->id)
            ->assertJsonMissingPath('data.videoUrl');
    }

    public function test_guest_user_can_see_session(): void
    {
        $session = Session::factory()->create();

        $this
            ->getJson(route('sessions.show', [
                'session' => $session
            ]))
            ->assertOk()
            ->assertJsonPath('data.id', $session->id)
            ->assertJsonPath('data.project.id', $session->project->id)
            ->assertJsonMissingPath('data.videoUrl');
    }

    public function test_company_member_can_assign_session_to_him(): void
    {
        $session = Session::factory()->unassigned()->create();

        $createdBy = $session->project->company->createdBy;

        Sanctum::actingAs($session->project->company->createdBy, ['*']);

        $this
            ->postJson(route('sessions.assignSessionToLoggedUser', [
                'session' => $session
            ]), [
                'userId' => $createdBy->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.id', $session->id)
            ->assertJsonPath('data.project.id', $session->project->id);

        $this->assertDatabaseHas((new Session())->getTable(), [
            'id' => $session->id,
            'created_by_id' => $createdBy->id,
        ]);
    }

    public function test_non_company_member_cant_assign_session_to_him(): void
    {
        $session = Session::factory()->unassigned()->create();

        Sanctum::actingAs(User::factory()->create(), ['*']);

        $this
            ->postJson(route('sessions.assignSessionToLoggedUser', [
                'session' => $session
            ]), [
                'userId' => $session->project->company->createdBy->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas((new Session())->getTable(), [
            'id' => $session->id,
            'created_by_id' => null,
        ]);
    }

    public function test_user_can_store_video(): void
    {
        $session = Session::factory()->create();

        $file = UploadedFile::fake()->create(
            'video.webm', 12000, 'video/webm'
        );

        Storage::fake('local');

        Queue::fake();

        Queue::assertNothingPushed();

        $this->postJson(route('sessions.video.store', [
            'projectSession' => $session
        ]), [
            'video' => $file
        ], [
            'x-devqaly-session-secret-token' => $session->secret_token
        ])
            ->assertOk()
            ->assertJsonPath('data.message', 'Video uploaded successfully!');

        Storage::disk('local')->assertExists(S3NamespacesEnum::VIDEOS_TO_CONVERT->value . "/$session->id.webm");

        Queue::assertPushed(function (ProcessSessionVideo $job) use ($session) {
            return $job->session->id === $session->id;
        });

    }

    public function test_user_cant_store_video_if_secret_token_is_not_passed(): void
    {
        $session = Session::factory()->create();

        $file = UploadedFile::fake()->create(
            'video.webm', 12000, 'video/webm'
        );

        Storage::fake('local');

        Queue::fake();

        Queue::assertNothingPushed();

        $this->postJson(route('sessions.video.store', [
            'projectSession' => $session
        ]), [
            'video' => $file
        ])
            ->assertForbidden();

        Storage::disk('local')->assertMissing(S3NamespacesEnum::VIDEOS_TO_CONVERT->value . "/$session->id.webm");

        Queue::assertNothingPushed();
    }
}
