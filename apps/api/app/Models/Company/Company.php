<?php

namespace App\Models\Company;

use App\Enum\Company\CompanyBlockedReasonEnum;
use App\Models\Project\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;

class Company extends Model
{
    use HasFactory, HasUuids, Billable;

    const MAX_NUMBER_COMPANIES_PER_USER = 50;

    protected $fillable = [
        'name',
        'created_by_id',
        'trial_ends_at',
        'billing_contact',
        'invoice_details',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'blocked_reasons' => 'json',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CompanyMember::class, 'company_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'company_id');
    }
}
