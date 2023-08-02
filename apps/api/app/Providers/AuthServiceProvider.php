<?php

namespace App\Providers;

use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\Project\Project;
use App\Models\Session\Session;
use App\Models\User;
use App\Policies\CompanyMemberPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\SessionPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Session::class => SessionPolicy::class,
        CompanyMember::class => CompanyMemberPolicy::class,
        Company::class => CompanyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return sprintf(
                '%s/%s/%s?email=%s',
                config('frontend.FRONTEND_BASE_URL'),
                'auth/resetPassword',
                $token,
                urlencode($user->email)
            );
        });
    }
}
