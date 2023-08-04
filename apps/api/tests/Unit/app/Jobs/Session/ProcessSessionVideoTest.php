<?php

namespace Tests\Unit\app\Jobs\Session;

use App\Enum\Sessions\SessionVideoStatusEnum;
use App\Jobs\Session\ProcessSessionVideo;
use App\Models\Session\Session;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProcessSessionVideoTest extends TestCase
{
    public function test_video_gets_processed(): void
    {
        $session = Session::factory()->create();

        $job = new ProcessSessionVideo($session);

        // We can't fake the disk here because we will be directly
        // calling `shell_exec` from within `ProcessSessionVideo::handle` method
        Storage::disk('local')
            ->copy(
                "videos-to-convert/test-video.webm",
                "videos-to-convert/{$session->id}.{$session->video_extension}"
            );

        $job->handle();

        $session->refresh();

        $this->assertEquals(32, $session->video_duration_in_seconds);
        $this->assertEqualsWithDelta(2.065318107605, $session->video_size_in_megabytes, 2);
        $this->assertEquals(100, $session->video_conversion_percentage);
        $this->assertEquals(SessionVideoStatusEnum::CONVERTED, $session->video_status);
        $this->assertNotNull($session->started_video_conversion_at);
        $this->assertNotNull($session->ended_video_conversion_at);

        Storage::assertExists("videos/{$session->id}.webm");
        Storage::delete("videos/{$session->id}.webm");
    }

    public function test_should_download_file_first_if_using_s3_filesystem(): void
    {
        $session = Session::factory()->create();

        $processVideoMock = Mockery::mock(ProcessSessionVideo::class, [$session])
            ->makePartial();

        $processVideoMock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('isUsingS3Bucket')
            ->zeroOrMoreTimes()
            ->andReturn(true);

        $processVideoMock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('downloadFileFromS3')
            ->once();

        $processVideoMock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('uploadFileToS3')
            ->once();

        // We can't fake the disk here because we will be directly
        // calling `shell_exec` from within `ProcessSessionVideo::handle` method
        Storage::disk('local')
            ->copy(
                "videos-to-convert/test-video.webm",
                "videos-to-convert/{$session->id}.{$session->video_extension}"
            );

        $processVideoMock->handle();

        $session->refresh();

        $this->assertEquals(100, $session->video_conversion_percentage);
        $this->assertEquals(SessionVideoStatusEnum::CONVERTED, $session->video_status);
        $this->assertNotNull($session->started_video_conversion_at);
        $this->assertNotNull($session->ended_video_conversion_at);

        Storage::assertExists("videos/{$session->id}.webm");
        Storage::delete("videos/{$session->id}.webm");
    }
}
