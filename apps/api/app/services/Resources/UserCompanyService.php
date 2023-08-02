<?php

namespace app\services\Resources;

use App\Models\Company\Company;
use App\Models\User;
use App\Traits\UsesPaginate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserCompanyService
{
    use UsesPaginate;

    public function listUserCompanies(Collection $filters, User $user)
    {
        $companies = Company::whereHas('members', function (Builder $query) use ($user) {
            $query->where('member_id', $user->id);
        });

        return $companies->paginate($this->getPerPage());
    }
}
