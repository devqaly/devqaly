<?php

namespace App\Policies;

use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Company $company): bool
    {
        return $company->members()->where('member_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CompanyMember $companyMember): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Company $company): bool
    {
        return $company->members()->where('member_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CompanyMember $companyMember): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CompanyMember $companyMember): Response
    {
        /** @var Company $company */
        $company = $companyMember->company;

        if ($company->members()->count() === 1) {
            return Response::deny('Company must have at least one member');
        }

        return $company->members()->where('member_id', $user->id)->exists()
            ? Response::allow()
            : Response::deny('You do not have the permissions');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CompanyMember $companyMember): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CompanyMember $companyMember): bool
    {
        //
    }
}
