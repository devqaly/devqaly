<?php

namespace App\Models\Session;

use App\Enum\Sessions\SessionVideoStatusEnum;
use App\Models\Project\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Session extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    const MAXIMUM_NUMBER_SESSIONS_FOR_FREE_COMPANIES = 50;

    protected $fillable = [
        'video_status',
        'video_extension',
        'video_conversion_percentage',
        'video_duration_in_seconds',
        'os',
        'platform_name',
        'version',
        'created_by_id',
        'project_id',
        'secret_token',
        'video_size_in_megabytes',
        'window_height',
        'window_width',
    ];

    protected $casts = [
        'video_status' => SessionVideoStatusEnum::class,
        'video_size_in_megabytes' => 'double'
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    protected static function booted(): void
    {
        static::saving(function (Session $session) {
            $session->secret_token = Str::random(60);
        });
    }
}
