<?php

namespace App\Models\Auth;

use App\Models\Company\CompanyMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RegisterToken extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['token', 'used_at', 'email', 'revoked', 'has_onboarding'];

    public function companyMember(): HasOne
    {
        return $this->hasOne(CompanyMember::class, 'register_token_id');
    }

    public function scopeByEmail(Builder $builder, string $email) {
        return $builder->where('email', $email);
    }

    public function scopeByUnrevoked(Builder $builder) {
        return $builder->where('revoked', 0);
    }

    public function scopeByValidToken(Builder $builder, string $token): Builder
    {
        return $builder->whereNull('used_at')
            ->where('revoked', false)
            ->where('token', $token);
    }

    public function getRouteKeyName()
    {
        return 'token';
    }
}

