<?php

namespace App\services\Resources\Company;

use App\Models\Company\Company;
use App\Traits\UsesPaginate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CompanyMemberService
{
    use UsesPaginate;

    public function listMembers(Collection $filters, Company $company): LengthAwarePaginator
    {
        $members = $company
            ->members()
            ->with(['invitedBy', 'member', 'registerToken'])
            ->when($filters->has('memberName'), function (Builder $builder) use ($filters) {
                $builder
                    ->whereHas('member', function (Builder $q) use ($filters) {
                        $q->where(
                            DB::raw("CONCAT(users.first_name, ' ', users.last_name)"),
                            'ILIKE',
                            $filters->get('memberName') . '%'
                        );
                    })
                    ->orWhereHas('registerToken', function (Builder $q) use ($filters) {
                        $q->where(
                            'email',
                            'LIKE',
                            $filters->get('memberName') . '%'
                        );
                    });
            })
            ->when($filters->has('invitedByName'), function (Builder $builder) use ($filters) {
                $builder
                    ->whereHas('invitedBy', function (Builder $q) use ($filters) {
                        $q->where(
                            DB::raw("CONCAT(users.first_name, ' ', users.last_name)"),
                            'ILIKE',
                            $filters->get('invitedByName') . '%'
                        );
                    });
            })
            ->when($filters->has('orderByCreatedAt'), function (Builder $builder) use ($filters) {
                $builder->orderBy('created_at', $filters->get('orderByCreatedAt'));
            });

        return $members->paginate($this->getPerPage());
    }
}
