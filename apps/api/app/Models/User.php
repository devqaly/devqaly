<?php

namespace App\Models;

use App\Models\Company\CompanyMember;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'first_name',
        'last_name',
        'timezone',
        'password',
        'email',
        'current_position',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => sprintf(
                '%s %s',
                $attributes['first_name'],
                $attributes['last_name']
            ),
        );
    }

    public function companiesMember(): HasMany
    {
        return $this->hasMany(CompanyMember::class, 'member_id');
    }
}
