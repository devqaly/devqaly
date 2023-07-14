<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterToken extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['token', 'used_at', 'email', 'revoked'];

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

