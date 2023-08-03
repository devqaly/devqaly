<?php

namespace App\Jobs\Session;

use App\Enum\S3NamespacesEnum;
use App\Enum\Sessions\SessionVideoStatusEnum;
use App\Models\Session\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\WebM;
use Illuminate\Support\Facades\Storage;

class ProcessSessionVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Session $session;

    public $timeout = 10000;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->processWebmVideo();
    }

    private function isUsingS3Bucket(): bool
    {
        return config('filesystems.default') === 's3';
    }

    private function isWebmVideo(): bool
    {
        return $this->session->video_extension === 'webm';
    }

    // Currently we are only accepting webm videos but we will leave this here in case
    // we need to transcode non-webm videos to webm
    private function processNotWebmVideo(): void
    {
        try {
            $ffmpeg = FFMpeg::create();
            $format = new WebM();

            $format->on('progress', function ($video, $format, $percentage) {
                $this->session->update(['video_conversion_percentage' => $percentage]);
            });

            $this->session->video_status = SessionVideoStatusEnum::CONVERTING;
            $this->session->started_video_conversion_at = now();

            $this->session->save();

            if ($this->isUsingS3Bucket()) {
                $s3File = Storage::disk('s3')->get("videos-to-convert/{$this->session->id}.{$this->session->video_extension}");
                $localDisk = Storage::disk('local');
                $localDisk->put("videos/{$this->session->id}.{$this->session->video_extension}", $s3File);
            }

            $inputPath = storage_path("app/videos/{$this->session->id}.{$this->session->video_extension}");
            $outputPath = storage_path("app/videos/{$this->session->id}.webm");

            $video = $ffmpeg->open($inputPath);
            $video->save($format, $outputPath);

            $ffprobe = FFProbe::create();
            $durationInSeconds = $ffprobe->format($outputPath)->get('duration');

            $this->session->video_status = SessionVideoStatusEnum::CONVERTED;
            $this->session->ended_video_conversion_at = now();
            $this->session->video_duration_in_seconds = floor($durationInSeconds);
            $this->session->save();

            if ($this->isUsingS3Bucket()) {
                $localFile = Storage::disk('local')->get("videos/{$this->session->id}.webm");
                Storage::disk('s3')->put("videos/{$this->session->id}.webm", $localFile);
                Storage::disk('s3')->delete("videos-to-convert/{$this->session->id}.{$this->session->video_extension}");
            }

            unlink($inputPath);

            // Since we upload the files to S3 there's no need to have a local copy
            if ($this->isUsingS3Bucket()) {
                unlink($outputPath);
            }
        } catch (\Exception $e) {
            Log::error("There was an error converting video for session: {$this->session->id}");

            throw $e;
        }
    }

    private function processWebmVideo(): void
    {
        try {
            $this->session->video_conversion_percentage = 100;
            $this->session->video_status = SessionVideoStatusEnum::CONVERTED;
            $this->session->started_video_conversion_at = now();
            $this->session->ended_video_conversion_at = now();

            if ($this->isUsingS3Bucket()) {
                $this->downloadFileFromS3();
            }

            $inputPath = storage_path("app/videos-to-convert/{$this->session->id}.{$this->session->video_extension}");
            $outputPath = storage_path("app/videos/{$this->session->id}.{$this->session->video_extension}");

            // The video being recorded by MediaRecorder in the browser sends headless information.
            // We need to copy the video with ffmpeg, so we can get meta information such as the duration of the video.
            // Actually convert the video, so we can get the metadata.
            shell_exec("ffmpeg -y -i $inputPath -vcodec copy -acodec copy $outputPath");

            $ffprobe = FFProbe::create();
            $durationInSeconds = $ffprobe->format($outputPath)->get('duration');

            $this->session->video_duration_in_seconds = floor($durationInSeconds);
            $this->session->video_size_in_megabytes = $this->getVideoSizeInMegabytes();
            $this->session->save();

            if ($this->isUsingS3Bucket()) {
                $this->uploadFileToS3();
            }

            $this->deleteOriginalVideo();
        } catch (\Exception $e) {
            Log::error("There was an error converting video for session: {$this->session->id}");

            throw $e;
        }
    }

    private function downloadFileFromS3(): void
    {
        $s3File = Storage::disk('s3')->get(sprintf("%s/%s", S3NamespacesEnum::VIDEOS_TO_CONVERT->value, "{$this->session->id}.{$this->session->video_extension}"));
        Storage::disk('local')->put("videos-to-convert/{$this->session->id}.{$this->session->video_extension}", $s3File);
    }

    private function uploadFileToS3(): void
    {
        $localFile = Storage::disk('local')->get("videos/{$this->session->id}.{$this->session->video_extension}");

        Storage::disk('s3')->put(sprintf(
            "%s/%s",
            S3NamespacesEnum::SESSION_VIDEOS->value,
            "{$this->session->id}.webm"
        ), $localFile);

        Storage::disk('s3')->delete(sprintf("%s/%s",
            S3NamespacesEnum::VIDEOS_TO_CONVERT->value,
            "{$this->session->id}.{$this->session->video_extension}"
        ));

        Storage::disk('local')->delete("videos/{$this->session->id}.{$this->session->video_extension}");
    }

    private function deleteOriginalVideo(): void
    {
        Storage::disk('local')->delete("videos-to-convert/{$this->session->id}.{$this->session->video_extension}");
    }

    private function getVideoSizeInMegabytes(): float
    {
        $videoSizeBytes = Storage::disk('local')->size("videos/{$this->session->id}.{$this->session->video_extension}");

        return $videoSizeBytes / 1048576;
    }
}
