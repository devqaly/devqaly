<?php

namespace App\Models\Company;

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
        'created_by_id'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CompanyMember::class, 'company_id');
    }
}
