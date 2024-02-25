<?php

namespace App\Models\Project;

use App\Models\Company\Company;
use App\Models\Session\Session;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'created_by_id',
        'company_id',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, 'project_id');
    }

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            $generatedProjectKey = Str::random(60);
            $generatedSecurityToken = Str::random(60);

            if (Project::where('project_key', $generatedProjectKey)->exists()) {
                $generatedProjectKey = Str::random(60);
            }

            if (Project::where('security_token', $generatedSecurityToken)->exists()) {
                $generatedSecurityToken = Str::random(60);
            }

            $project->project_key = $generatedProjectKey;
            $project->security_token = $generatedSecurityToken;
        });
    }
}
