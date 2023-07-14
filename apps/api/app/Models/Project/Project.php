<?php

namespace App\Models\Project;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, HasUuids;

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

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            $generatedProjectKey = Str::random(60);

            if (Project::where('project_key', $generatedProjectKey)->exists()) {
                $generatedProjectKey = Str::random(60);
            }

            $project->project_key = $generatedProjectKey;
        });
    }
}
