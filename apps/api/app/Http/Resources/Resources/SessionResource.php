<?php

namespace App\Http\Resources\Resources;

use App\Enum\S3NamespacesEnum;
use App\Enum\Sessions\SessionVideoStatusEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'os' => $this->os,
            'platformName' => $this->platform_name,
            'version' => $this->version,
            'videoStatus' => $this->video_status,
            'startedVideoConversionAt' => $this->started_video_conversion_at,
            'endedVideoConversionAt' => $this->ended_video_conversion_at,
            'videoDurationInSeconds' => $this->video_duration_in_seconds,
            'windowWidth' => $this->window_width,
            'windowHeight' => $this->window_height,
            'videoUrl' => $this->getVideoUrl(),
            'createdAt' => $this->created_at,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'createdBy' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }

    private function getVideoUrl(): ?string
    {
        if ($this->video_status !== SessionVideoStatusEnum::CONVERTED) {
            return null;
        }

        if (config('filesystems.default') === 's3') {
            return Storage::temporaryUrl(
                sprintf('%s/%s', S3NamespacesEnum::SESSION_VIDEOS->value, "$this->id.webm"),
                Carbon::now()->addHours(2)
            );
        }

        return route('sessions.streamVideo', ['session' => $this->resource]);
    }
}
