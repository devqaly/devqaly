<?php

namespace App\Models\Company;

use App\Models\Auth\RegisterToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyMember extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'member_id',
        'register_token_id',
        'invited_by_id'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function registerToken(): BelongsTo
    {
        return $this->belongsTo(RegisterToken::class, 'register_token_id');
    }
}
